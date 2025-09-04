<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * Add mappings here if/when you use them, e.g.:
     *  \App\Models\Opportunity::class => \App\Policies\OpportunityPolicy::class,
     */
    protected $policies = [
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', fn($user) => $user && (($user->role ?? null) === 'admin'));
    }
}
