<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagedAccountService
{
    public function create(array $data, string $role): User
    {
        if (! in_array($role, ['staff', 'instructor'], true)) {
            throw new \InvalidArgumentException('Unsupported account role');
        }

        return DB::transaction(function () use ($data, $role) {
            $user = $data['account_mode'] === 'new' ? User::create(array_intersect_key($data, array_flip(['name', 'email', 'password']))) : User::where('email', $data['email'])->lockForUpdate()->firstOrFail();
            if ($role === 'staff' && $user->roles()->where('name', 'admin')->exists()) {
                throw ValidationException::withMessages(['email' => 'لا تُدار حسابات المديرين من إدارة الموظفين.']);
            }
            $user->roles()->syncWithoutDetaching([Role::where('name', $role)->firstOrFail()->id]);
            if ($role === 'instructor') {
                $user->instructorProfile()->firstOrCreate(['user_id' => $user->id], array_intersect_key($data, array_flip(['professional_title', 'biography', 'experience_years'])));
            }

            return $user;
        }, 3);
    }
}
