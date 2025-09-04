<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:login')->only('login');
    }

    public function show(Request $request)
    {
        // If already admin, go straight to dashboard
        $u = Auth::user();
        if ($u && Gate::forUser($u)->allows('isAdmin')) {
            return redirect()->route('admin.dashboard');
        }
        return response()->view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
            'remember' => ['nullable','boolean'],
        ]);
        $remember = (bool)($data['remember'] ?? false);

        if (!Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']], $remember)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        // Only allow admins here
        if (!Gate::forUser(Auth::user())->allows('isAdmin')) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is not an administrator']);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
