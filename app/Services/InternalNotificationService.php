<?php

namespace App\Services;

use App\Models\NotificationMessage;
use App\Models\TrainingGroup;

class InternalNotificationService
{
    // Called only inside the transaction that makes an actual state transition.
    public function send(array $userIds, string $type, string $title, string $body): void
    {
        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        if (! $userIds) {
            return;
        }
        $message = NotificationMessage::create(compact('type', 'title', 'body'));
        $message->recipients()->createMany(array_map(fn ($id) => ['user_id' => $id], $userIds));
    }

    public function lecture(TrainingGroup $group, string $body): void
    {
        $ids = $group->applications()->whereHas('enrollment', fn ($q) => $q->where('status', 'active'))->pluck('student_user_id')->all();
        $ids[] = $group->instructor_user_id;
        $this->send($ids, 'lecture_changed', 'تحديث محاضرة المجموعة '.$group->code, $body);
    }
}
