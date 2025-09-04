<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class EnforceAdminGuard
{
    public function handle($request, Closure $next)
    {
        $adminHost = env('ADMIN_DOMAIN', 'admin.swaeduae.ae');
        $onAdminHost = $request->getHost() === $adminHost;

        $path = ltrim($request->path(), '/');                 // e.g. "admin/login" or "admin/users"
        $isAdminPath = Str::startsWith($path, 'admin');

        if ($onAdminHost && $isAdminPath) {
            // Allow admin login page without auth
            if (Str::startsWith($path, 'admin/login')) {
                return $next($request);
            }
            // Everything else under /admin/* requires auth + access-admin
            if (!Auth::check()) {
                return redirect()->away('https://' . $adminHost . '/admin/login');
            }
            if (Gate::denies('access-admin')) {
                abort(403);
            }
        }

        return $next($request);
    }
}
