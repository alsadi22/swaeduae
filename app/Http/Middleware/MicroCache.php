<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MicroCache
{
    public function handle(Request $request, Closure $next)
    {
        // Skip non-GET, admin and auth pages entirely
        if ($request->method() !== 'GET'
            || $request->is('admin*')
            || $request->is('login') || $request->is('logout')
            || $request->is('register') || $request->is('password/*')) {
            return $next($request);
        }

        // No caching for now — pass through safely
        return $next($request);
    }
}
