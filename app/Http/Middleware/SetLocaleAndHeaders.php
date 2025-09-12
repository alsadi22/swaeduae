<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleAndHeaders
{
    public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    if (method_exists($response, 'headers')) {
        try {
            $locale = app()->getLocale();
            $response->headers->set('Content-Language', $locale);

            $vary = $response->headers->get('Vary');
            if ($vary) {
                if (stripos($vary, 'Accept-Language') === false) {
                    $response->headers->set('Vary', $vary . ',Accept-Language');
                }
            } else {
                $response->headers->set('Vary', 'Accept-Language');
            }

            $cc = $response->headers->get('Cache-Control');
            if (!$response->headers->has('Cache-Control') || stripos((string)$cc, 'public') === false) {
                $response->headers->set('Cache-Control', 'no-cache, private');
            }
        } catch (\Throwable $e) {
            \Log::warning('SetLocaleAndHeaders skipped: '.$e->getMessage());
        }
    }

    return $response;
}
}
