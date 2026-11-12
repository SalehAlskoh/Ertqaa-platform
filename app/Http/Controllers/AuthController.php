<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function storeRegistration(RegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));
            $user->roles()->attach(Role::where('name', 'student')->firstOrFail());
            $user->studentProfile()->create(['phone_number' => $request->validated('phone_number')]);

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile.edit')->setStatusCode(303)->with('status', 'تم إنشاء حساب الطالب.');
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string'], 'remember' => ['nullable', 'boolean']]);
        $data['email'] = mb_strtolower(trim($data['email']));
        $key = 'login:'.hash('sha256', $data['email'].'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'محاولات كثيرة. حاول بعد '.RateLimiter::availableIn($key).' ثانية.']);
        }
        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'بيانات الدخول غير صحيحة أو الحساب غير نشط.']);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        return redirect()->route($user->hasRole('admin', 'staff') ? 'admin.courses.index' : ($user->hasRole('instructor') ? 'instructor.groups.index' : 'profile.edit'));
    }
}
