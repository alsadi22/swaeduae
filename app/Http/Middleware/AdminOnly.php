<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Require admin guard session
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('toast', 'Please sign in as admin.');
        }

        // Optional: if your Admin model has a boolean/ability, enforce it.
        $user = Auth::guard('admin')->user();
        if (property_exists($user, 'is_admin') && !$user->is_admin) {
            abort(403);
        }

        return $next($request);
    }
}
