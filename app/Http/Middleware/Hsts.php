<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Hsts
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('app.env') === 'production' && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
