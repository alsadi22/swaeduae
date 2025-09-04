<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$router = app('router');

printf("%-6s %-25s %-40s %s\n", 'METHOD','URI','NAME','MIDDLEWARE');
foreach ($router->getRoutes() as $route) {
    $uri = $route->uri();
    if (!preg_match('#(^|/)login$|(^|/)register$#', $uri)) continue;
    if (!in_array('POST', $route->methods(), true)) continue;

    $name = $route->getName() ?? '-';
    $mw   = implode(', ', $route->gatherMiddleware());
    printf("%-6s %-25s %-40s %s\n", 'POST', $uri, $name, $mw);
}
