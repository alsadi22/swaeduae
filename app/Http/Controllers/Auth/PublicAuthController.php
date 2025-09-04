<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login_public');
    }

    public function login(Request $r)
    {
        $data = $r->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        if (Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']], (bool)($data['remember'] ?? false))) {
            $r->session()->regenerate();
            return redirect()->intended(url('/my/profile'));
        }

        return back()->withErrors(['email'=>'Invalid credentials'])->withInput();
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect(url('/'));
    }
}
