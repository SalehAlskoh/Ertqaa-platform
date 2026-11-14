<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-catalog', fn (User $u) => $u->hasRole('admin', 'staff'));
        Gate::define('manage-accounts', fn (User $u) => $u->hasRole('admin'));
        Gate::define('teach', fn (User $u) => $u->hasRole('instructor'));
        Gate::define('study', fn (User $u) => $u->hasRole('student'));
    }
}
