<?php
$kernel = __DIR__ . '/../app/Http/Kernel.php';
$prov   = __DIR__ . '/../app/Providers/AppServiceProvider.php';

function add_alias_to_kernel($file) {
    $src = file_get_contents($file);
    if ($src === false) return false;

    if (strpos($src, "'admin' => \\App\\Http\\Middleware\\AdminMiddleware::class") !== false) {
        echo "Kernel: admin alias already present.\n";
        return true;
    }

    $added = false;

    // Prefer Laravel 11 style: $middlewareAliases
    if (preg_match('/protected\\s+\\$middlewareAliases\\s*=\\s*\\[/', $src)) {
        $src = preg_replace(
            '/(protected\\s+\\$middlewareAliases\\s*=\\s*\\[)/',
            "$1\n        'admin' => \\\\App\\\\Http\\\\Middleware\\\\AdminMiddleware::class,",
            $src, 1, $count
        );
        if ($count > 0) $added = true;
    }
    // Fallback: older $routeMiddleware
    if (!$added && preg_match('/protected\\s+\\$routeMiddleware\\s*=\\s*\\[/', $src)) {
        $src = preg_replace(
            '/(protected\\s+\\$routeMiddleware\\s*=\\s*\\[)/',
            "$1\n        'admin' => \\\\App\\\\Http\\\\Middleware\\\\AdminMiddleware::class,",
            $src, 1, $count
        );
        if ($count > 0) $added = true;
    }

    if ($added) {
        file_put_contents($file, $src);
        echo "Kernel: admin alias inserted.\n";
        return true;
    }

    echo "Kernel: could not find middleware aliases array.\n";
    return false;
}

function ensure_provider_alias($file) {
    if (!file_exists($file)) {
        // Create provider with aliasing in boot()
        $tpl = <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ensure 'admin' middleware alias exists even if Kernel edit fails
        Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
    }
}
PHP;
        file_put_contents($file, $tpl);
        echo "AppServiceProvider created with alias.\n";
        return;
    }

    $src = file_get_contents($file);
    if (strpos($src, 'Route::aliasMiddleware(\'admin\'') !== false) {
        echo "AppServiceProvider: alias already present.\n";
        return;
    }
    if (strpos($src, 'use Illuminate\\Support\\Facades\\Route;') === false) {
        $src = preg_replace('/namespace\\s+App\\\\Providers;\\s*/', "namespace App\\Providers;\n\nuse Illuminate\\Support\\Facades\\Route;\n", $src, 1);
    }
    $src = preg_replace('/public function boot\\(.*?\\)\\s*:\\s*void\\s*\\{/', '$0' . "\n        Route::aliasMiddleware('admin', \\App\\Http\\Middleware\\AdminMiddleware::class);", $src, 1);
    file_put_contents($file, $src);
    echo "AppServiceProvider: alias injected.\n";
}

add_alias_to_kernel($kernel);
ensure_provider_alias($prov);
