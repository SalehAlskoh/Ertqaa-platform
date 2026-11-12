<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $u): bool
    {
        return $u->hasRole('admin', 'staff');
    }

    public function view(?User $u, Course $c): bool
    {
        return $c->is_published || ($u?->hasRole('admin', 'staff') ?? false);
    }

    public function create(User $u): bool
    {
        return $this->viewAny($u);
    }

    public function update(User $u, Course $c): bool
    {
        return $this->viewAny($u);
    }

    public function delete(User $u, Course $c): bool
    {
        return $this->viewAny($u) && ! $c->trainingGroups()->exists() && ! $c->studentAnnouncements()->exists();
    }
}
