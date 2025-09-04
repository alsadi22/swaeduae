<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function guard()
    {
        // Default "web" guard
        return Auth::guard();
    }

    protected function authenticated(Request $request, $user)
    {
        // Avoid stale intended URLs
        $request->session()->forget('url.intended');

        $isAdmin = (bool)($user->is_admin ?? false) || (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isOrg   = (($user->role ?? null) === 'organization') || (method_exists($user, 'hasRole') && $user->hasRole('org'));

        if ($isAdmin) {
            return redirect('/admin');
        }
        if ($isOrg) {
            return redirect()->route('org.dashboard');
        }
        return redirect()->route('profile');
    }

    public function __construct()
    {
        // Keep it simple while we test login
        $this->middleware('throttle:login')->only('login');
        $this->middleware('guest')->except('logout');
    }
}
