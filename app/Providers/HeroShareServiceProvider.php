<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class HeroShareServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        // Make sure  exists for any view that expects it
        if (!View::shared("hero")) {
            View::share("hero", []);
        }
    }
}
