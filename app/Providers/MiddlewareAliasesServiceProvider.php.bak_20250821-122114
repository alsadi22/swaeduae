<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MiddlewareAliasesServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        // Aliases (idempotent – reassigns safely)
        $router->aliasMiddleware('honeypot', \App\Http\Middleware\Honeypot::class);
        $router->aliasMiddleware('microcache', \App\Http\Middleware\MicroCache::class);

                $router->aliasMiddleware('admin.only', \App\Http\Middleware\AdminOnly::class);
// Ensure microcache runs for all "web" routes (guests & public-only logic is inside the middleware)
        $router->pushMiddlewareToGroup('web', 'microcache:120');
    }
}