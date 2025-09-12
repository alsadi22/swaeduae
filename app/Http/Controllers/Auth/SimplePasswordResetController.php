<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SimplePasswordResetController extends Controller
{
    public function show(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', '')
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token'    => ['required','string'],
            'email'    => ['required','email'],
            'password' => ['required','confirmed','min:8'],
        ]);

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(str()->random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // auto-login optional; keep it simple → redirect to login with success
            return redirect('/login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
