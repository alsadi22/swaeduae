<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceOrgRegistration
{
    public function handle(Request $request, Closure $next)
    {
        // Never apply on admin or auth endpoints
        if ($request->is('admin*')
            || $request->is('login') || $request->is('logout')
            || $request->is('register') || $request->is('password/*')
            || $request->is('reset-password/*') || $request->is('contact')) {
            return $next($request);
        }

        // If not logged in, let auth/verified middlewares handle redirects
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // (Reserved for future org checks)
        return $next($request);
    }
}
