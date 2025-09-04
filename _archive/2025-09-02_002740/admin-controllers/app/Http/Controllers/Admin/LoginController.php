<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.root');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = $request->user();
            $isAdmin = false;
            if (method_exists($user, 'hasRole')) {
                try {
                    $isAdmin = $user->hasRole('admin');
                } catch (\Throwable $e) {
                    \Log::warning('ADMIN_LOGIN_PROBE: hasRole check failed: '.$e->getMessage());
                    $isAdmin = false;
                }
            }
            if ($isAdmin) {
                return redirect()->intended('/admin');
            }
            Auth::logout();
            return back()->withErrors(['email' => __('You do not have access to the admin area.')])->onlyInput('email');
        }

        return back()->withErrors(['email' => __('Invalid credentials.')])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
