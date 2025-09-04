<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $locale = app()->getLocale() ?: 'en';
        $response->headers->set('Content-Language', $locale);

        // Merge Vary without duplicating entries
        $existing = $response->headers->get('Vary');
        $varyParts = array_filter(array_map('trim', explode(',', (string)$existing)));
        if (!in_array('Accept-Language', array_map('strtolower', $varyParts))) {
            $varyParts[] = 'Accept-Language';
        }
        $response->headers->set('Vary', implode(', ', array_filter($varyParts)));

        return $response;
    }
}
