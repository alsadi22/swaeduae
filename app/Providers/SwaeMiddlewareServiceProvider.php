<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class SwaeMiddlewareServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(Router $router): void
    {
        // Push our header+locale normalizer early in the web pipeline.
        $router->pushMiddlewareToGroup('web', \App\Http\Middleware\SetLocaleAndHeaders::class);

        // If MicroCache middleware class exists, push it late in the web group so headers from controllers are finalized first.
        if (class_exists(\App\Http\Middleware\MicroCache::class)) {
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\MicroCache::class);
        }
    }
}
