<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class SpatieAliasesServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        $router->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);
        $router->aliasMiddleware('permission', \App\Http\Middleware\EnsureRole::class);
        $router->aliasMiddleware('role_or_permission', \App\Http\Middleware\EnsureRole::class);
    }
}
