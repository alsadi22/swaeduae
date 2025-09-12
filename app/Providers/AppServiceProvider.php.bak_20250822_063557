<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $id = (string) ($request->input('email') ?? 'guest');
            return [Limit::perMinute(5)->by($id.'|'.$request->ip())];
        });

        RateLimiter::for('global', function (Request $request) {
            return [Limit::perMinute(120)->by($request->ip())];
        });

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Shared vars so any layout/partial/error never crashes
        View::composer('*', function ($view) {
            $view->with('assetV', config('app.asset_version', '1'));
            $view->with('rtl', in_array(app()->getLocale(), ['ar','fa','ur','he']));
        });

        if (class_exists(Paginator::class)) {
            Paginator::useBootstrapFive();
        }
    }
}
