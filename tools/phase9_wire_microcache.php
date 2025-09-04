<?php
// 1) Ensure provider file contains alias + push-to-group
$path = 'app/Providers/MiddlewareAliasesServiceProvider.php';
@mkdir(dirname($path), 0777, true);
$contents = <<<'PHP2'
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MiddlewareAliasesServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        // Aliases (idempotent – reassigns safely)
        $router->aliasMiddleware('honeypot', \App\Http\Middleware\Honeypot::class);
        $router->aliasMiddleware('microcache', \App\Http\Middleware\MicroCache::class);

        // Ensure microcache runs for all "web" routes (guests & public-only logic is inside the middleware)
        $router->pushMiddlewareToGroup('web', 'microcache:120');
    }
}
PHP2;

if (!file_exists($path) || strpos(file_get_contents($path), 'microcache') === false) {
    @copy($path, $path.'.bak_'.date('Ymd_His'));
    file_put_contents($path, $contents);
    echo "[OK] MiddlewareAliasesServiceProvider written\n";
} else {
    echo "[SKIP] Provider already contains microcache\n";
}

// 2) Ensure provider is registered in config/app.php
$f = 'config/app.php';
$s = file_get_contents($f);
if (strpos($s, 'MiddlewareAliasesServiceProvider::class') === false) {
    $s = preg_replace("/('providers'\\s*=>\\s*\\[)/",
        "$1\n        \\App\\Providers\\MiddlewareAliasesServiceProvider::class,",
        $s, 1, $c);
    if ($c) {
        copy($f, "$f.bak_".date('Ymd_His'));
        file_put_contents($f, $s);
        echo "[OK] Provider registered in config/app.php\n";
    } else {
        echo "[WARN] Could not patch config/app.php (manual check)\n";
    }
} else {
    echo "[SKIP] Provider already registered\n";
}
