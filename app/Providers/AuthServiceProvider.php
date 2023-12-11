<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-teachers', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-units', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-classes', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-grade-descriptors', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-indicators', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-academic-terms', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
