<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // You can define your gates here
        Gate::define('admin-access', function ($user) {
            return $user->user_role === 'Admin';
        });

        Gate::define('educator-access', function ($user) {
            return $user->user_role === 'Educator';
        });

        Gate::define('training-access', function ($user) {
            return $user->user_role === 'Training';
        });

        Gate::define('student-access', function ($user) {
            return $user->user_role === 'Student';
        });
    }
}