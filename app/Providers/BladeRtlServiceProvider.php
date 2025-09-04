<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeRtlServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Blade::if('rtl', fn() => view()->shared('isRtl') === true);
        Blade::if('ltr', fn() => view()->shared('isRtl') === false);
    }
}
