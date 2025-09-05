<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin-access', function ($user) {
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('admin');
            }
            return (($user->role ?? null) === 'admin');
        });

        Gate::define('org-access', function ($user) {
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('org');
            }
            return (($user->role ?? null) === 'org');
        });
    }
}
