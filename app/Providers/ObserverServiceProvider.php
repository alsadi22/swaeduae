<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\OrgProfile;
use App\Observers\OrgProfileObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        // Registering an observer is lightweight and safe in tests.
        if (class_exists(OrgProfile::class)) {
            OrgProfile::observe(OrgProfileObserver::class);
        }
    }
}
