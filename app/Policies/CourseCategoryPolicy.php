<?php

namespace App\Policies;

use App\Models\CourseCategory;
use App\Models\User;

class CourseCategoryPolicy
{
    public function create(User $u): bool
    {
        return $u->hasRole('admin', 'staff');
    }

    public function update(User $u, CourseCategory $c): bool
    {
        return $this->create($u);
    }

    public function delete(User $u, CourseCategory $c): bool
    {
        return $this->create($u) && ! $c->courses()->exists();
    }
}
