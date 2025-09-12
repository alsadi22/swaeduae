<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGate
{
    public function handle(Request $request, Closure $next)
    {
        // Only act on /admin paths
        if ($request->is('admin') || $request->is('admin/')) {
            return $next($request);
        }

        if ($request->is('admin/*')) {
            // Allow login/logout endpoints
            if ($request->is('admin/login') || $request->is('admin/logout')) {
                return $next($request);
            }

            // Must be logged in
            $user = Auth::user();
            if (!$user) {
                return redirect()->guest('/admin/login');
            }

            // Optional: require verified email
            if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // Must have admin role
            if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
                abort(403);
            }
        }

        return $next($request);
    }
}
