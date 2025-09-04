<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If user is trying to see an admin page, send them to admin area
                if ($request->is('admin') || $request->is('admin/*')) {
                    return redirect('/admin');
                }
                // If they came via org pages, send to org dashboard
                if ($request->is('org') || $request->is('org/*')) {
                    return redirect('/org/dashboard');
                }
                // Otherwise treat as volunteer
                return redirect('/profile');
            }
        }
        return $next($request);
    }
}
