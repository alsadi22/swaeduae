<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);
        if (Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.root'));
        }
        return back()->withErrors(['email'=>__('Invalid credentials')])->withInput(['email'=>$data['email']]);
    }

    public function logout(Request $request)
    {
        try { Auth::guard('admin')->logout(); } catch (\Throwable $e) {}
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}

