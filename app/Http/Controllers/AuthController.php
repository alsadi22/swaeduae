<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $type = $request->query('type');
        if (!in_array($type, ['org','volunteer'])) $type = null;
        return view('auth.login', ['type' => $type]);
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($cred, $remember)) {
            $request->session()->regenerate();

            $uid = Auth::id();
            $hasOrg = DB::table('organizations')->where('owner_user_id', $uid)->exists()
                   || DB::table('organization_users')->where('user_id', $uid)->exists();

            if ($request->input('type') === 'org' || $hasOrg) {
                return redirect()->intended('/org/dashboard');
            }
            return redirect()->intended('/profile');
        }

        return back()->withErrors(['email' => __('auth.failed')])
                     ->withInput($request->only('email','type'));
    }

    public function showRegister(Request $request)
    {
        $type = $request->query('type');
        if (!in_array($type, ['org','volunteer'])) $type = null;
        return view('auth.register', ['type' => $type]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required', Password::min(8)],
            'type'     => ['nullable','in:org,volunteer'],
        ]);

        $id = DB::table('users')->insertGetId([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Auth::loginUsingId($id);
        $request->session()->regenerate();

        return ($data['type'] ?? null) === 'org'
            ? redirect('/org/dashboard')
            : redirect('/profile');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
