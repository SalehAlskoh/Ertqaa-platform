<?php

namespace App\Policies;

use App\Models\StudentAnnouncement;
use App\Models\User;

class StudentAnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    public function view(User $user, StudentAnnouncement $announcement): bool
    {
        return $this->viewAny($user)
            || StudentAnnouncement::visibleToStudent($user)->whereKey($announcement->id)->exists()
            || StudentAnnouncement::visibleToInstructor($user)->whereKey($announcement->id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, StudentAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }

    public function publish(User $user, StudentAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }

    public function unpublish(User $user, StudentAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }
}
