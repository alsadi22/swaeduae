<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    // Generic provider redirect (google, facebook, twitter, linkedin)
    public function redirect(string $provider)
    {
        try {
            if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
                return redirect('/volunteer/login')->with('error', 'Social login not configured.');
            }
            return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            return redirect('/volunteer/login')->with('error', 'Social login unavailable.');
        }
    }

    // Generic provider callback
    public function callback(string $provider)
    {
        try {
            if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
                return redirect('/volunteer/login')->with('error', 'Social login not configured.');
            }
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
            $email = $socialUser->getEmail();
            if (!$email) {
                return redirect('/volunteer/login')->with('error', 'No email returned by provider.');
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Volunteer'),
                    'password' => bcrypt(Str::random(32)),
                    'email_verified_at' => now(),
                ]
            );

            // Mark email verified for Google users
            if ($provider === "google") {
                $gVerified = true; // default true for Google
                try { $gVerified = (bool)($socialUser->user["verified_email"] ?? true); } catch (\Throwable $e) { $gVerified = true; }
                if ($gVerified && is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }

            // Default role: volunteer
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('volunteer');
            }

            Auth::login($user, true);
            return redirect()->intended('/profile');
        } catch (\Throwable $e) {
            return redirect('/volunteer/login')->with('error', 'Social login failed.');
        }
    }

    // UAE PASS redirect (stub-friendly)
    public function uaePassRedirect()
    {
        // If you have a Socialite UAEPASS provider wired, use it; otherwise fail gracefully
        if (config('services.uaepass.client_id')) {
            try {
                return \Laravel\Socialite\Facades\Socialite::driver('uaepass')->redirect();
            } catch (\Throwable $e) {
                return redirect('/volunteer/login')->with('error', 'UAE PASS not available.');
            }
        }
        return redirect('/volunteer/login')->with('error', 'UAE PASS not configured.');
    }

    // UAE PASS callback (stub-friendly)
    public function uaePassCallback()
    {
        try {
            if (!config('services.uaepass.client_id')) {
                return redirect('/volunteer/login')->with('error', 'UAE PASS not configured.');
            }
            $up = \Laravel\Socialite\Facades\Socialite::driver('uaepass')->user();
            $email = $up->getEmail() ?: null;

            // UAE PASS sometimes returns national ID only; fallback to a pseudo email if needed
            if (!$email && method_exists($up, 'getId')) {
                $email = $up->getId().'@uaepass.local';
            }
            if (!$email) {
                return redirect('/volunteer/login')->with('error', 'UAE PASS did not supply an identifier.');
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $up->getName() ?: 'UAE PASS User',
                    'password' => bcrypt(Str::random(32)),
                    'email_verified_at' => now(),
                ]
            );
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('volunteer');
            }
            Auth::login($user, true);
            return redirect()->intended('/profile');
        } catch (\Throwable $e) {
            return redirect('/volunteer/login')->with('error', 'UAE PASS sign-in failed.');
        }
    }
}
