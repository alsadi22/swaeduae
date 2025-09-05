<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OverrideCsp
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = implode(' ', [
            "default-src 'self';",
            "img-src 'self' data:;",
            "style-src 'self' 'unsafe-inline';",
            "script-src 'self' 'unsafe-inline' https://plausible.io https://www.google.com https://www.gstatic.com;",
            "font-src 'self' data:;",
            "connect-src 'self' https://plausible.io https://www.google.com https://www.gstatic.com;",
            "frame-src 'self' https://www.google.com;",
            "frame-ancestors 'self';",
            "upgrade-insecure-requests",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        return $response;
    }
}
