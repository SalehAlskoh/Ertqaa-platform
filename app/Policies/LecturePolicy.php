<?php

namespace App\Policies;

use App\Models\Lecture;
use App\Models\User;

class LecturePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    public function view(User $user, Lecture $lecture): bool
    {
        return $this->create($user)
            || ($user->hasRole('instructor') && $lecture->trainingGroup->instructor_user_id === $user->id)
            || ($user->hasRole('student') && $lecture->trainingGroup->applications()->where('student_user_id', $user->id)->whereHas('enrollment', fn ($q) => $q->where('status', 'active'))->exists());
    }

    public function update(User $user, Lecture $lecture): bool
    {
        return $this->create($user);
    }
}
