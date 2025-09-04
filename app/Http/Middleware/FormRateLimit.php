<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class FormRateLimit
{
    protected int $max = 5;   // requests (align with no-drift rule)
    protected int $decay = 60; // seconds
    protected array $paths = [
        'register',
        'org/register',
        'contact',
        'volunteer/*/register',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->is($this->paths)) {
            $key = 'forms:'.$request->ip().':'.($request->path() ?: 'root');

            if (RateLimiter::tooManyAttempts($key, $this->max)) {
                $retry = RateLimiter::availableIn($key);
                return response('Too Many Requests', 429)->header('Retry-After', $retry);
            }
            RateLimiter::hit($key, $this->decay);
        }

        return $next($request);
    }
}
