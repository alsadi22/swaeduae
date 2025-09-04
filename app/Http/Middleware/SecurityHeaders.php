<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // NOTE: $response->headers is a property (ResponseHeaderBag), not a method.
        if (isset($response) && isset($response->headers)) {
            try {
                // Language + Vary
                $locale = app()->getLocale() ?: 'en';
                $response->headers->set('Content-Language', $locale);

                $vary = (string) $response->headers->get('Vary', '');
                if ($vary !== '') {
                    if (stripos($vary, 'Accept-Language') === false) {
                        $response->headers->set('Vary', rtrim($vary, ',') . ',Accept-Language');
                    }
                } else {
                    $response->headers->set('Vary', 'Accept-Language');
                }

                // Cache-Control (dynamic pages default)
                $cc = (string) $response->headers->get('Cache-Control', '');
                if ($cc === '' || stripos($cc, 'public') === false) {
                    $response->headers->set('Cache-Control', 'no-cache, private');
                }

                // MicroCache marker
                $xm = trim((string) $response->headers->get('X-MicroCache', ''));
                if ($xm === '') {
                    $response->headers->set('X-MicroCache', 'SKIP');
                }

                // Mild, safe security headers
                $response->headers->set(
                    'Content-Security-Policy',
                    "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' https://plausible.io https://www.google.com https://www.gstatic.com; font-src 'self' data:; connect-src 'self' https://plausible.io https://www.google.com https://www.gstatic.com; frame-src 'self' https://www.google.com; frame-ancestors 'self'; upgrade-insecure-requests"
                );
                if ($request->isSecure()) {
                    $response->headers->set('Strict-Transport-Security', 'max-age=86400; includeSubDomains; preload');
                }
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                // Explicitly disable powerful features by default
                $response->headers->set('Permissions-Policy', 'geolocation=(), camera=()');
            } catch (\Throwable $e) {
                \Log::warning('SecurityHeaders skipped: '.$e->getMessage());
            }
        }

        return $response;
    }
}
