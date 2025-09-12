<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class RateLimitServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // 5 attempts per minute per IP+email; 1 minute decay
        RateLimiter::for('login', function (Request $request) {
            $key = sha1(($request->input('email') ?? 'guest').'|'.$request->ip());
            return [ Limit::perMinute(5)->by($key)->response(function() {
                return response('Too many login attempts. Please try again in a minute.', 429);
            }) ];
        });
    }
}
