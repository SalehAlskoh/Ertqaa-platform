<?php

namespace App\Services;

use App\Models\Course;
use App\Models\TrainingGroup;
use App\Models\User;
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
            $locked = $group ? TrainingGroup::whereKey($group->id)->lockForUpdate()->firstOrFail() : new TrainingGroup;
            $course = Course::whereKey($data['course_id'])->lockForUpdate()->firstOrFail();
            $instructor = User::whereKey($data['instructor_user_id'])->lockForUpdate()->firstOrFail();
            if (! $instructor->hasRole('instructor') || ! $instructor->instructorProfile()->exists()) {
                throw ValidationException::withMessages(['instructor_user_id' => 'اختر محاضرًا نشطًا يمتلك ملفًا مهنيًا ودور محاضر.']);
            }
            if ($data['status'] === 'published' && ! $course->is_published) {
                throw ValidationException::withMessages(['status' => 'يجب نشر الكورس قبل نشر المجموعة.']);
            }
            if ($locked->exists) {
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
