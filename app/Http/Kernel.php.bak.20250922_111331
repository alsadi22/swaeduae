<?php

namespace App\Http;

use App\Http\Middleware\EnforceAdminGuard;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\AdminLoginProbe::class,
        \App\Http\Middleware\SecurityHeaders::class,
        \App\Http\Middleware\Hsts::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\NoCacheLoginResponses::class,
            \App\Http\Middleware\VerifiedPathEnforcer::class,
            \App\Http\Middleware\AdminPathEnforcer::class,
            \App\Http\Middleware\SetLocale::class,

            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            EnforceAdminGuard::class,
            \App\Http\Middleware\SetLocaleFromSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            \App\Http\Middleware\FormRateLimit::class,
            // /* App\Http\Middleware\MicroCache::class, */
            \App\Http\Middleware\AdminGate::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware aliases.
     */
    protected $middlewareAliases = [
        'nocache' => \App\Http\Middleware\NoCacheLoginResponses::class,
        // Laravel defaults
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // App-specific
        'form.ratelimit' => \App\Http\Middleware\FormRateLimit::class,
        'readonly' => \App\Http\Middleware\ReadOnlyMode::class,
        'org' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    ];
}
