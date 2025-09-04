<?php

namespace App\Providers;

use App\Services\Socialite\UaePassProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class UaePassSocialiteServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Socialite::extend('uaepass', function ($app) {
            $cfg = $app['config']['services.uaepass'];
            return new UaePassProvider(
                $app['request'],
                $cfg['client_id'],
                $cfg['client_secret'],
                $cfg['redirect']
            );
        });
    }
}
