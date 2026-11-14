<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('admin', 'staff') || ($user->hasRole('student') && $enrollment->application->student_user_id === $user->id);
    }

    public function withdraw(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('student') && $enrollment->application->student_user_id === $user->id;
    }
}
