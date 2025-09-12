<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {
        \Illuminate\Support\Facades\RateLimiter::for('forms', function(\Illuminate\Http\Request $request){
            return [\Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->ip())];
        });
        // 5 attempts/minute keyed by email + IP
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower((string) $request->input('email')).'|'.$request->ip();
            return [ Limit::perMinute(5)->by($key) ];
        });
    }
}
