<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Allow only users with is_admin=1 OR role 'admin'/'superadmin'
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        $isAdminFlag = $user && property_exists($user, 'is_admin') && (int)($user->is_admin) === 1;

        $isAdminRole = false;
        if ($user && method_exists($user, 'hasAnyRole')) {
            $isAdminRole = $user->hasAnyRole(['admin', 'superadmin']);
        }

        if ($isAdminFlag || $isAdminRole) {
            return $next($request);
        }

        abort(403, 'ADMINS ONLY');
    }
}
