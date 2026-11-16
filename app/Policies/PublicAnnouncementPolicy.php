<?php

namespace App\Policies;

use App\Models\PublicAnnouncement;
use App\Models\User;

class PublicAnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    public function view(?User $user, PublicAnnouncement $announcement): bool
    {
        return $announcement->publication_status === 'published' || ($user?->hasRole('admin', 'staff') ?? false);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, PublicAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }

    public function publish(User $user, PublicAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }

    public function unpublish(User $user, PublicAnnouncement $announcement): bool
    {
        return $this->viewAny($user);
    }
}
