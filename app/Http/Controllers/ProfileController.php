<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $r)
    {
        return view('profile.edit', ['user' => $r->user(), 'studentProfile' => $r->user()->studentProfile]);
    }

    public function update(Request $r)
    {
        $rules = ['name' => ['required', 'string', 'max:150'], 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($r->user()->id)]];
        if ($r->user()->hasRole('student')) {
            $rules += ['phone_number' => ['required', 'string', 'max:30'], 'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'], 'education_level' => ['nullable', 'string', 'max:100'], 'institution_name' => ['nullable', 'string', 'max:150'], 'city' => ['nullable', 'string', 'max:100']];
        }
        $data = $r->validate($rules);
        DB::transaction(function () use ($r, $data) {
            $user = $r->user();
            if ($user->email !== $data['email']) {
                $user->email_verified_at = null;
            }
            $user->fill(array_intersect_key($data, array_flip(['name', 'email'])))->save();
            if ($user->hasRole('student')) {
                $user->studentProfile()->updateOrCreate(['user_id' => $user->id], array_diff_key($data, array_flip(['name', 'email'])));
            }
        });

        return back()->setStatusCode(303)->with('status', 'تم تحديث الملف الشخصي.');
    }

    public function password(Request $r)
    {
        $data = $r->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', Password::min(12), 'different:current_password']]);
        $r->user()->forceFill(['password' => $data['password'], 'remember_token' => Str::random(60)])->save();
        $r->session()->regenerate();

        return back()->setStatusCode(303)->with('status', 'تم تحديث كلمة المرور.');
    }
}
