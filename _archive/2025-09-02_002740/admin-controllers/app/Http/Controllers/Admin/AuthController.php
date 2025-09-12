<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        Auth::shouldUse('admin'); // important

        $creds    = $request->only('email','password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($creds, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.root'); // always go to /admin
        }

        return back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
