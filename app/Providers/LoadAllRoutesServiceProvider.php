<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LoadAllRoutesServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        $all = glob(base_path('routes/*.php'));
        $skip = ['console.php','api.php','web.php']; // web.php is already loaded by bootstrap/app.php
        foreach ($all as $file) {
            $base = basename($file);
            if (in_array($base, $skip)) continue;
            if (str_starts_with($base, 'web.backup')) continue;
            if (is_dir(base_path('routes/_disabled')) && file_exists(base_path('routes/_disabled/'.$base))) continue;
            Route::middleware('web')->group($file);
        }
    }
}
