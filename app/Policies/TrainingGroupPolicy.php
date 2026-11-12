<?php

namespace App\Policies;

use App\Models\TrainingGroup;
use App\Models\User;

class TrainingGroupPolicy
{
    public function view(User $u, TrainingGroup $g): bool
    {
        return $u->hasRole('admin', 'staff') || ($u->hasRole('instructor') && $u->id === $g->instructor_user_id);
    }

    public function create(User $u): bool
    {
        return $u->hasRole('admin', 'staff');
    }

    public function update(User $u, TrainingGroup $g): bool
    {
        return $this->create($u);
    }
}
