<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function redirectTo()
    {
        $user = Auth::user();

        // If user is organization role → org dashboard
        if ($user->hasRole('org')) {
            return '/org/dashboard';
        }

        // If admin → admin dashboard
        if ($user->hasRole('admin')) {
            return '/admin/dashboard';
        }

        // Default for all others (volunteers, etc.)
        return '/';
    }

    public function __construct()
    {
        $this->middleware([\App\Http\Middleware\Honeypot::class, 'throttle:login'])->only('login');

        $this->middleware('guest')->except('logout');
    }
}
