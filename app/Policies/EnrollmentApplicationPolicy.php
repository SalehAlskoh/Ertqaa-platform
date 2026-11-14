<?php

namespace App\Policies;

use App\Models\EnrollmentApplication;
use App\Models\User;

class EnrollmentApplicationPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('student') && $user->studentProfile()->exists();
    }

    public function view(User $user, EnrollmentApplication $application): bool
    {
        return $user->hasRole('admin', 'staff') || ($user->hasRole('student') && $user->id === $application->student_user_id);
    }

    public function review(User $user, EnrollmentApplication $application): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    public function confirm(User $user, EnrollmentApplication $application): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    public function cancel(User $user, EnrollmentApplication $application): bool
    {
        return $user->hasRole('student') && $user->id === $application->student_user_id;
    }
}
