<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Content-Language (idempotent)
        $locale = app()->getLocale() ?: config('app.locale', 'en');
        if (! $response->headers->has('Content-Language')) {
            $response->headers->set('Content-Language', $locale);
        }

        // Ensure Vary contains Accept-Language (merge, don't clobber)
        $existingVary = (string) $response->headers->get('Vary', '');
        if (stripos($existingVary, 'Accept-Language') === false) {
            $newVary = trim($existingVary ? $existingVary . ', Accept-Language' : 'Accept-Language');
            $response->headers->set('Vary', $newVary);
        }

        // Micro-cache diagnostic header:
        // - skip when request carries cookies or is not safe (e.g., POST/PUT/DELETE)
        $hasCookie   = $request->headers->has('Cookie') || count($request->cookies->all()) > 0;
        $methodSafe  = in_array($request->getMethod(), ['GET', 'HEAD'], true);

        if (! $response->headers->has('X-MicroCache')) {
            $response->headers->set('X-MicroCache', ($hasCookie || ! $methodSafe) ? 'SKIP' : 'MISS');
        }

        return $response;
    }
}
