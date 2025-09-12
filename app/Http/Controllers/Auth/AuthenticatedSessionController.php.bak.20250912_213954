<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required','string','email'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        // Force admin to /admin (do NOT use intended)
        if ($user && (($user->role ?? null) === 'admin')) {
            Log::info('admin_login',[ 'uid'=> $user->id, 'email'=> $user->email ]);
            return redirect(Route::has('admin.dashboard') ? route('admin.dashboard') : '/admin');
        }

        // Force org to /org (do NOT use intended)
        if ($user && (($user->role ?? null) === 'org')) {
            return redirect(Route::has('org.dashboard') ? route('org.dashboard') : '/org');
        }

        // Volunteers -> profile (fallback to /)
        return redirect(Route::has('my.profile') ? route('my.profile') : '/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->intended(AppProvidersRouteServiceProvider::HOME);
    }
}
