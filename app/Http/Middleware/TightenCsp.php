<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class TightenCsp
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // No CDNs, no unsafe-eval. Keep inline styles for now to avoid regressions.
        $policy = "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self';";

        // Append (do not replace) so it coexists with any edge header.
        $response->headers->set('Content-Security-Policy', $policy, false);

        // Common security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', "geolocation=(self), microphone=(), camera=()");
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        return $response;
    }
}
