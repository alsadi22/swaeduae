<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPathEnforcer
{
    public function handle(Request $request, Closure $next)
    {
        // ALLOW admin login/logout passthrough
        if ($request->is('admin/login') || $request->is('admin/logout')) { return $next($request); }
        if ($request->is('admin') || $request->is('admin/*')) {
            // Allow the guest login page itself to render
            if ($request->is('admin/login')) {
                return $next($request);
            }
            if (!auth('admin')->check()) {
                // send to admin login, preserve intended (absolute https)
                session(['url.intended' => $request->fullUrl()]);
                return redirect()->to('https://swaeduae.ae/admin/login');
            }
            $u = auth('admin')->user();
            $isAdminFlag = $u && property_exists($u, 'is_admin') && (int)$u->is_admin === 1;
            $isAdminRole = $u && method_exists($u, 'hasAnyRole') && $u->hasAnyRole(['admin','superadmin']);
            if (!($isAdminFlag || $isAdminRole)) {
                abort(403, 'ADMINS ONLY');
            }
        }
        return $next($request);
    }
}
