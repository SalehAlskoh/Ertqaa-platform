<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\EnrollmentApplication;
use App\Models\TrainingGroup;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function __construct(private TrainingGroupService $groups, private InternalNotificationService $notifications) {}

    private function fail(string $message, string $field = 'application'): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function apply(User $actor, TrainingGroup $group): EnrollmentApplication
    {
        Gate::forUser($actor)->authorize('create', EnrollmentApplication::class);

        return DB::transaction(function () use ($actor, $group) {
            $group = TrainingGroup::whereKey($group->id)->lockForUpdate()->firstOrFail();
            if ($group->status !== 'published' || ! $group->course->is_published || now()->lt($group->registration_opens_at) || now()->gte($group->registration_closes_at)) {
                $this->fail('المجموعة لا تقبل طلبات التسجيل حاليًا.');
            }
            if ($group->applications()->where('student_user_id', $actor->id)->exists()) {
                $this->fail('لديك طلب سابق لهذه المجموعة؛ لا يمكن إعادة التقديم.');
            }
            [$occupied, $reserved] = $this->groups->occupancy($group);
            if ($occupied + $reserved >= $group->capacity) {
                $this->fail('لا تتوفر مقاعد حاليًا.');
            }

            return $group->applications()->create(['student_user_id' => $actor->id, 'status' => 'pending']);
        }, 3);
    }

    private function locked(EnrollmentApplication $application): array
    {
        $group = TrainingGroup::whereKey($application->training_group_id)->lockForUpdate()->firstOrFail();
        $application = EnrollmentApplication::whereKey($application->id)->lockForUpdate()->firstOrFail();
        abort_unless($application->training_group_id === $group->id, 409);

        return [$group, $application];
    }

    public function approve(User $actor, EnrollmentApplication $application): EnrollmentApplication
    {
        Gate::forUser($actor)->authorize('review', $application);

        return DB::transaction(function () use ($actor, $application) {
            [$group, $application] = $this->locked($application);
            if ($application->effective_status === 'awaiting_confirmation') {
                return $application; // Retry never extends the deadline.
            }
            if ($application->status !== 'pending') {
                $this->fail('الموافقة المبدئية متاحة للطلبات قيد المراجعة فقط.');
            }
            if (in_array($group->status, ['cancelled', 'completed', 'draft'], true) || ! $group->course->is_published) {
                $this->fail('حالة المجموعة أو الكورس لا تسمح بقبول الطلب.');
            }
            [$occupied, $reserved] = $this->groups->occupancy($group);
            if ($occupied + $reserved >= $group->capacity) {
                $this->fail('لا يوجد مقعد متاح للموافقة المبدئية.');
            }
            $application->forceFill(['status' => 'awaiting_confirmation', 'reviewed_by_user_id' => $actor->id, 'reviewed_at' => now(), 'offer_expires_at' => now()->addHours(48)])->save();
            $this->notifications->send([$application->student_user_id], 'application_approved', 'قبول مبدئي لطلبك', 'حُجز مقعد في المجموعة '.$group->code.' لمدة 48 ساعة. راجع المركز لاستكمال التحصيل والتسجيل الحضوري.');

            return $application;
        }, 3);
    }

    public function reject(User $actor, EnrollmentApplication $application, string $reason): EnrollmentApplication
    {
        Gate::forUser($actor)->authorize('review', $application);
        if (trim($reason) === '' || strlen($reason) > 65000) {
            $this->fail('أدخل سبب رفض مختصرًا وصحيحًا.', 'rejection_reason');
        }

        return DB::transaction(function () use ($actor, $application, $reason) {
            [$group, $application] = $this->locked($application);
            if ($application->status === 'rejected') {
                return $application;
            }
            if (! in_array($application->effective_status, ['pending', 'awaiting_confirmation'], true)) {
                $this->fail('لا يمكن رفض هذا الطلب في حالته الحالية.');
            }
            $application->forceFill(['status' => 'rejected', 'rejection_reason' => $reason, 'reviewed_by_user_id' => $actor->id, 'reviewed_at' => now()])->save();
            $this->notifications->send([$application->student_user_id], 'application_rejected', 'نتيجة مراجعة طلبك', 'رُفض طلب المجموعة '.$group->code.'. السبب: '.$reason);

            return $application;
        }, 3);
    }

    public function cancel(User $actor, EnrollmentApplication $application): EnrollmentApplication
    {
        Gate::forUser($actor)->authorize('cancel', $application);

        return DB::transaction(function () use ($application) {
            [, $application] = $this->locked($application);
            if ($application->status === 'cancelled') {
                return $application;
            }
            if (! in_array($application->effective_status, ['pending', 'awaiting_confirmation'], true)) {
                $this->fail('لا يمكن إلغاء الطلب في حالته الحالية.');
            }
            $application->forceFill(['status' => 'cancelled'])->save();

            return $application;
        }, 3);
    }

    public function confirm(User $actor, EnrollmentApplication $application, mixed $paidAmount): Enrollment
    {
        Gate::forUser($actor)->authorize('confirm', $application);
        abort_unless(auth()->id() === $actor->id, 403);

        return DB::transaction(function () use ($application, $paidAmount) {
            [$group, $application] = $this->locked($application);
            $existing = $application->enrollment()->lockForUpdate()->first();
            if ($application->status === 'completed' && $existing) {
                return $existing; // No financial data or notification is rewritten.
            }
            if ($existing || $application->effective_status !== 'awaiting_confirmation') {
                $this->fail('لا يوجد عرض محجوز صالح للتأكيد؛ تحقق من الحالة ومهلة 48 ساعة.');
            }
            if (in_array($group->status, ['cancelled', 'completed', 'draft'], true)) {
                $this->fail('حالة المجموعة لا تسمح بتأكيد التسجيل.');
            }
            if ($group->fee_amount === null) {
                $this->fail('يجب تحديد رسوم المجموعة صراحة قبل التأكيد.', 'paid_amount');
            }
            $fee = Money::cents($group->fee_amount);
            $paid = Money::cents($paidAmount);
            if ($fee !== $paid || ($fee > 0 && ! preg_match('/^[A-Z]{3}$/D', (string) $group->currency_code))) {
                $this->fail('يجب استلام كامل رسوم المجموعة بعملتها المحددة، دون خصم أو سداد جزئي.', 'paid_amount');
            }
            [$occupied, $reserved] = $this->groups->occupancy($group);
            if ($occupied + $reserved > $group->capacity || $occupied >= $group->capacity || $reserved < 1) {
                $this->fail('السعة الحالية لا تسمح بتأكيد هذا الحجز.');
            }
            $time = now();
            if ($application->offer_expires_at->lte($time)) {
                $this->fail('انتهت مهلة العرض قبل اعتماد العملية؛ لم يُسجل أي تحصيل.');
            }
            $enrollment = new Enrollment;
            $enrollment->forceFill(['application_id' => $application->id, 'status' => 'active', 'enrolled_at' => $time, 'confirmed_by_user_id' => auth()->id(), 'paid_amount' => Money::decimal($paid), 'paid_at' => $fee > 0 ? $time : null])->save();
            $application->forceFill(['status' => 'completed'])->save();
            $this->notifications->send([$application->student_user_id], 'enrollment_confirmed', 'اكتمل تسجيلك في المركز', 'تم تأكيد تسجيلك الحضوري في المجموعة '.$group->code.'. يمكنك الآن مشاهدة محاضرات المجموعة.');

            return $enrollment;
        }, 3);
    }

    public function withdraw(User $actor, Enrollment $enrollment): Enrollment
    {
        Gate::forUser($actor)->authorize('withdraw', $enrollment);

        $application = $enrollment->application;

        return DB::transaction(function () use ($enrollment, $application) {
            $this->locked($application);
            $enrollment = Enrollment::whereKey($enrollment->id)->lockForUpdate()->firstOrFail();
            if ($enrollment->status !== 'active') {
                $this->fail('الانسحاب متاح لتسجيل نشط فقط.');
            }
            $enrollment->forceFill(['status' => 'withdrawn', 'withdrawn_at' => now()])->save();

            return $enrollment;
        }, 3);
    }

    public function expire(EnrollmentApplication $application): bool
    {
        return DB::transaction(function () use ($application) {
            [$group, $application] = $this->locked($application);
            if ($application->status !== 'awaiting_confirmation' || $application->effective_status !== 'expired') {
                return false;
            }
            $application->forceFill(['status' => 'expired'])->save();
            $this->notifications->send([$application->student_user_id], 'application_expired', 'انتهت مهلة الحجز', 'انتهت مهلة العرض للمجموعة '.$group->code.' وتحرر المقعد دون تسجيل نهائي.');

            return true;
        }, 3);
    }
}
