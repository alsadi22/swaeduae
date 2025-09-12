<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = $guards ?: [null];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                // Hard redirect by role (ignores "intended")
                if (($user->role ?? null) === 'admin') {
                    return redirect("/admin");
                }
                if (($user->role ?? null) === 'org') {
                    return redirect("/admin");
                }
                // volunteers/public
                return redirect("/admin");
            }
        }

        return $next($request);
    }
}
