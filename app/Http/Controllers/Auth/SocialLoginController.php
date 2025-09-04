<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $social = Socialite::driver($provider)->stateless()->user();

        $email = $social->getEmail();
        // If provider doesn't send email (rare), bail gracefully.
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Your '.$provider.' account did not provide an email.']);
        }

        $name  = $social->getName() ?: $social->getNickname() ?: 'User';
        $user  = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => bcrypt(Str::random(40)),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);
        return redirect()->intended('/profile');
    }
}
