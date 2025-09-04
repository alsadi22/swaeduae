<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            // Decide target by role; intended() overrides if set by routes
            $u = \Illuminate\Support\Facades\Auth::user();
            $target = "/profile";
            if ($u) {
                if (method_exists($u, "hasRole") && ($u->hasRole("admin") || $u->hasRole("org"))) {
                    $target = "/admin";
                } elseif (property_exists($u, "is_admin") && $u->is_admin) {
                    $target = "/admin";
                }
            }
            return redirect()->intended($target);
            // Send user where they were going, else volunteer dashboard if exists, else home
            $target = route_exists('volunteer.profile') ? route('volunteer.profile') : url('/');
            return redirect()->intended($target);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => __('These credentials do not match our records.')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

/** Helper: route_exists without importing Facade inside Blade */
if (!function_exists('route_exists')) {
    function route_exists(string $name): bool
    {
        try { return \Illuminate\Support\Facades\Route::has($name); }
        catch (\Throwable $e) { return false; }
    }
}
