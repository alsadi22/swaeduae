<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleLoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function perform(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','string','email'],
            'password' => ['required','string'],
        ]);
        $remember = (bool)$request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            // If admin, send to /admin; else home
            if (method_exists(Auth::user(),'hasRole') && Auth::user()->hasRole('admin')) {
                return redirect()->intended("/admin");
            }
            return redirect()->intended("/admin");
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/admin");
    }
}
