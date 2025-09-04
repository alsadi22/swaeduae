<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MiddlewareAliasServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Route::aliasMiddleware('auth', \App\Http\Middleware\Authenticate::class);
    }
}
