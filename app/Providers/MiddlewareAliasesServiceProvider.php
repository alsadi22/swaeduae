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
        $router->aliasMiddleware('setlocaleheaders', \App\Http\Middleware\SetLocaleAndHeaders::class);
        $router->aliasMiddleware('admin.only', \App\Http\Middleware\AdminOnly::class);
        $router->aliasMiddleware('form.ratelimit', \App\Http\Middleware\FormRateLimit::class);

        // Ensure setlocaleheaders runs before microcache
        $router->pushMiddlewareToGroup('web', 'setlocaleheaders');
        if (config('app.microcache_enabled')) {
            $router->pushMiddlewareToGroup('web', 'microcache');
        }
    }
}
