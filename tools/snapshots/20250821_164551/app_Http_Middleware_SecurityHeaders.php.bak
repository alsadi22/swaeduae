<?php
namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders {
  public function handle($request, Closure $next): Response {
    $r = $next($request);
    $h = $r->headers;

    // Only set CSP here; other headers come from server to avoid duplicates
    if (!$h->has('Content-Security-Policy')) {
      $h->set('Content-Security-Policy',
        "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; ".
        "script-src 'self' 'unsafe-inline'; font-src 'self' data:; connect-src 'self'; ".
        "frame-ancestors 'self'; upgrade-insecure-requests"
      );
    }
    return $r;
  }
}
