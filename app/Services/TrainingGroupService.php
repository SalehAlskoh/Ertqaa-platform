<?php

namespace App\Services;

use App\Models\Course;
use App\Models\TrainingGroup;
use App\Models\User;
use App\Support\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrainingGroupService
{
    public function occupancy(TrainingGroup $group): array
    {
        $occupied = $group->applications()->whereHas('enrollment', fn ($q) => $q->where('status', 'active'))->count();
        $reserved = $group->applications()->where('status', 'awaiting_confirmation')->where('offer_expires_at', '>', now())->whereDoesntHave('enrollment', fn ($q) => $q->where('status', 'active'))->count();

        return [$occupied, $reserved];
    }

    public function save(array $data, ?TrainingGroup $group = null): TrainingGroup
    {
        foreach (['registration_opens_at', 'registration_closes_at'] as $field) {
            $data[$field] = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data[$field], 'Africa/Tripoli')->utc()->format('Y-m-d H:i:00');
        }
        if ($data['fee_amount'] === null) {
            $data['currency_code'] = null;
        }

        return DB::transaction(function () use ($data, $group) {
            $previousInstructor = $group?->instructor_user_id;
            $instructorIds = array_values(array_unique(array_filter([$previousInstructor, (int) $data['instructor_user_id']])));
            sort($instructorIds);
            $instructors = User::whereIn('id', $instructorIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $locked = $group ? TrainingGroup::whereKey($group->id)->lockForUpdate()->firstOrFail() : new TrainingGroup;
            if ($locked->exists && $locked->instructor_user_id !== $previousInstructor) {
                throw ValidationException::withMessages(['instructor_user_id' => 'تغير إسناد المجموعة؛ حدّث الصفحة وأعد المحاولة.']);
            }
            $course = Course::whereKey($data['course_id'])->lockForUpdate()->firstOrFail();
            $instructor = $instructors->get((int) $data['instructor_user_id']);
            if (! $instructor || ! $instructor->hasRole('instructor') || ! $instructor->instructorProfile()->exists()) {
                throw ValidationException::withMessages(['instructor_user_id' => 'اختر محاضرًا نشطًا يمتلك ملفًا مهنيًا ودور محاضر.']);
            }
            if ($data['status'] === 'published' && ! $course->is_published) {
                throw ValidationException::withMessages(['status' => 'يجب نشر الكورس قبل نشر المجموعة.']);
            }
            if ($locked->exists) {
                $financialHistory = $locked->applications()->whereHas('enrollment', fn ($q) => $q->whereNotNull('paid_amount')->orWhereNotNull('paid_at'))->exists();
                $feeChanged = ($locked->fee_amount === null) !== ($data['fee_amount'] === null)
                    || ($locked->fee_amount !== null && $data['fee_amount'] !== null && Money::cents($locked->fee_amount) !== Money::cents($data['fee_amount']));
                if ($financialHistory && ($feeChanged || $locked->currency_code !== $data['currency_code'])) {
                    throw ValidationException::withMessages(['fee_amount' => 'لا يمكن تغيير الرسوم أو العملة بعد وجود تسجيلات مالية مؤكدة، حتى بعد الانسحاب.']);
                }
                if ($locked->instructor_user_id !== (int) $data['instructor_user_id']) {
                    $lectures = $locked->lectures()->where('status', 'scheduled')->get();
                    foreach ($lectures as $lecture) {
                        app(LectureService::class)->assertNoConflict((int) $data['instructor_user_id'], CarbonImmutable::instance($lecture->starts_at), CarbonImmutable::instance($lecture->ends_at), null, $locked->id);
                    }
                }
                [$occupied,$reserved] = $this->occupancy($locked);
                if ($data['capacity'] < $occupied + $reserved) {
                    throw ValidationException::withMessages(['capacity' => 'السعة أقل من التسجيلات النشطة والحجوزات الحالية.']);
                }
            }
            $locked->fill($data)->save();

            return $locked;
        }, 3);
    }
}
