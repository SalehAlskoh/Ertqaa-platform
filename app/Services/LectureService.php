<?php

namespace App\Services;

use App\Models\Lecture;
use App\Models\TrainingGroup;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class LectureService
{
    public function __construct(private InternalNotificationService $notifications) {}

    private function fail(string $message, string $field = 'starts_at'): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function time(string $input): CarbonImmutable
    {
        try {
            $time = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $input, 'Africa/Tripoli');
            if ($time->format('Y-m-d\TH:i') !== $input) {
                $this->fail('التاريخ أو الوقت غير صالح.');
            }

            return $time->utc();
        } catch (\Throwable $exception) {
            if ($exception instanceof ValidationException) {
                throw $exception;
            }
            $this->fail('التاريخ أو الوقت غير صالح.');
        }
    }

    private function lockGroup(TrainingGroup $group): TrainingGroup
    {
        // The user row serializes scheduling across every group of this instructor.
        $instructorId = $group->instructor_user_id;
        User::whereKey($instructorId)->lockForUpdate()->firstOrFail();
        $locked = TrainingGroup::whereKey($group->id)->lockForUpdate()->firstOrFail();
        if ($locked->instructor_user_id !== $instructorId) {
            $this->fail('تغير إسناد المحاضر أثناء العملية؛ حدّث الصفحة وأعد المحاولة.', 'training_group_id');
        }

        return $locked;
    }

    public function assertNoConflict(int $instructorId, CarbonImmutable $start, CarbonImmutable $end, ?int $exceptLecture = null, ?int $exceptGroup = null): void
    {
        if ($end->lte($start)) {
            $this->fail('يجب أن تأتي نهاية المحاضرة بعد بدايتها.', 'ends_at');
        }
        $conflict = Lecture::where('status', 'scheduled')
            ->whereHas('trainingGroup', fn ($q) => $q->where('instructor_user_id', $instructorId))
            ->when($exceptLecture, fn ($q) => $q->where('id', '!=', $exceptLecture))
            ->when($exceptGroup, fn ($q) => $q->where('training_group_id', '!=', $exceptGroup))
            ->where('starts_at', '<', $end)->where('ends_at', '>', $start)->exists();
        if ($conflict) {
            $this->fail('يتعارض الموعد مع محاضرة مجدولة للمحاضر.');
        }
    }

    public function generate(User $actor, TrainingGroup $group, array $data): void
    {
        Gate::forUser($actor)->authorize('create', Lecture::class);
        $first = $this->time($data['first_date'].'T'.$data['start_time']);
        $firstLocal = $first->setTimezone('Africa/Tripoli');
        $weekdays = array_unique(array_map('intval', $data['weekdays']));
        if (! $weekdays || array_diff($weekdays, range(0, 6)) || ! in_array($firstLocal->dayOfWeek, $weekdays, true)) {
            $this->fail('تاريخ أول محاضرة يجب أن يوافق أحد أيام التدريب المختارة.', 'first_date');
        }
        if ($data['count'] < 1 || $data['count'] > 200 || $data['end_time'] <= $data['start_time']) {
            $this->fail('اختر من 1 إلى 200 محاضرة ووقت نهاية بعد البداية.', 'count');
        }
        DB::transaction(function () use ($group, $data, $firstLocal, $weekdays) {
            $group = $this->lockGroup($group);
            if ($group->lectures()->exists()) {
                $this->fail('المجموعة لديها محاضرات بالفعل؛ لا يمكن توليد جدول آخر فوقها.', 'training_group_id');
            }
            $day = $firstLocal->startOfDay();
            for ($number = 1; $number <= (int) $data['count']; $number++) {
                while (! in_array($day->dayOfWeek, $weekdays, true)) {
                    $day = $day->addDay();
                }
                $start = $this->time($day->format('Y-m-d').'T'.$data['start_time']);
                $end = $this->time($day->format('Y-m-d').'T'.$data['end_time']);
                $this->assertNoConflict($group->instructor_user_id, $start, $end);
                $group->lectures()->create(['lecture_number' => $number, 'starts_at' => $start, 'ends_at' => $end, 'status' => 'scheduled']);
                $day = $day->addDay();
            }
        }, 3);
    }

    public function create(User $actor, TrainingGroup $group, array $data): Lecture
    {
        Gate::forUser($actor)->authorize('create', Lecture::class);

        return DB::transaction(function () use ($group, $data) {
            $group = $this->lockGroup($group);
            $start = $this->time($data['starts_at']);
            $end = $this->time($data['ends_at']);
            $this->assertNoConflict($group->instructor_user_id, $start, $end);
            $number = ((int) $group->lectures()->max('lecture_number')) + 1;
            if ($number > 65535) {
                $this->fail('وصل ترقيم المجموعة إلى الحد الأقصى.', 'training_group_id');
            }

            return $group->lectures()->create(['lecture_number' => $number, 'title' => $data['title'] ?? null, 'starts_at' => $start, 'ends_at' => $end, 'status' => 'scheduled']);
        }, 3);
    }

    public function change(User $actor, Lecture $lecture, array $data, string $operation = 'reschedule'): Lecture
    {
        Gate::forUser($actor)->authorize('update', $lecture);
        if ($operation !== 'complete' && (trim($data['change_reason'] ?? '') === '' || strlen($data['change_reason']) > 65000)) {
            $this->fail('أدخل سبب تغيير مختصرًا وصحيحًا.', 'change_reason');
        }

        $group = $lecture->trainingGroup;

        return DB::transaction(function () use ($lecture, $group, $data, $operation) {
            $group = $this->lockGroup($group);
            $lecture = Lecture::whereKey($lecture->id)->lockForUpdate()->firstOrFail();
            if (($operation === 'cancel' && $lecture->status === 'cancelled') || ($operation === 'complete' && $lecture->status === 'completed')) {
                return $lecture;
            }
            if ($lecture->status !== 'scheduled') {
                $this->fail('لا يمكن جدولة أو تغيير محاضرة مكتملة أو ملغاة.');
            }
            if ($operation === 'reschedule') {
                $start = $this->time($data['starts_at']);
                $end = $this->time($data['ends_at']);
                $this->assertNoConflict($group->instructor_user_id, $start, $end, $lecture->id);
                $changed = ! $start->equalTo($lecture->starts_at) || ! $end->equalTo($lecture->ends_at);
                $lecture->fill(['starts_at' => $start, 'ends_at' => $end, 'title' => $data['title'] ?? null, 'change_reason' => $data['change_reason']])->save();
                if ($changed) {
                    $this->notifications->lecture($group, 'تغير موعد المحاضرة رقم '.$lecture->lecture_number.'؛ راجع جدول المجموعة. السبب: '.$data['change_reason']);
                }
            } elseif ($operation === 'cancel') {
                $lecture->fill(['status' => 'cancelled', 'change_reason' => $data['change_reason']])->save();
                $this->notifications->lecture($group, 'أُلغيت المحاضرة رقم '.$lecture->lecture_number.'. السبب: '.$data['change_reason']);
            } elseif ($operation === 'complete') {
                $lecture->fill(['status' => 'completed'])->save();
            } else {
                abort(422);
            }

            return $lecture;
        }, 3);
    }
}
