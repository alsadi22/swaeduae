<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    // GET /login
    public function create()
    {
        $view = (request()->getHost()==='admin.swaeduae.ae') ? 'auth.admin_login' : 'auth.login';
        return view($view);
    }

    // POST /login
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','string','email'],
            'password' => ['required','string'],
        ]);
        $remember = (bool)$request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $request->session()->regenerate();
        return redirect()->intended('/my/profile');
    }

    // POST /logout
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
