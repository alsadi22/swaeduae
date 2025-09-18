<?php
namespace App\Providers;
use App\Models\OrgProfile;
use App\Observers\OrgProfileObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
        if (config('sentry.dsn')) {
            $this->app->register(\Sentry\Laravel\ServiceProvider::class);
        }
    }

    public function boot(): void {
        if (class_exists(AppModelsOrgProfile::class)) {
            AppModelsOrgProfile::observe(AppObserversOrgProfileObserver::class);
        }
// Force Spatie Permission to use acl_* tables if present
        config([
            'permission.table_names.roles' => 'acl_roles',
            'permission.table_names.permissions' => 'acl_permissions',
            'permission.table_names.model_has_roles' => 'acl_model_has_roles',
            'permission.table_names.model_has_permissions' => 'acl_model_has_permissions',
            'permission.table_names.role_has_permissions' => 'acl_role_has_permissions',
        ]);

        
        if (!View::shared("hero")) { View::share("hero", []); }
RateLimiter::for('forms', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

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
