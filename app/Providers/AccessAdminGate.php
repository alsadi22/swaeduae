<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AccessAdminGate extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            // 1) boolean column
            if (property_exists($user, 'is_admin') && $user->is_admin) return true;
            // 2) role helper (Spatie or custom)
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
            // 3) fallback: env email allowlist
            $allow = array_filter(array_map('trim', explode(',', (string) env('ADMIN_ADMINS', ''))));
            return $allow && in_array(strtolower($user->email), array_map('strtolower', $allow), true);
        });
    }
}
