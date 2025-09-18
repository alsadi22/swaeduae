<?php
namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AdminAccessGateProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        if (!Gate::has('admin-access')) {
            Gate::define('admin-access', function ($user) {
                return method_exists($user,'hasRole') ? $user->hasRole('admin') : false;
            });
        }
    }
}
