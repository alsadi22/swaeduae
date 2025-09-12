<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$router = app('router');
$routes = $router->getRoutes();
$all    = method_exists($routes, 'getRoutes') ? $routes->getRoutes() : $routes;

printf("%-6s %-25s %-40s %s\n", 'METHOD','URI','NAME','MIDDLEWARE');
foreach ($all as $route) {
    $uri = $route->uri();
    if (!in_array('POST', $route->methods(), true)) continue;
    if (!preg_match('#(^|/)login$|(^|/)register$#', $uri)) continue;

    $name = $route->getName() ?? '-';
    $mw   = implode(', ', $route->gatherMiddleware());
    printf("%-6s %-25s %-40s %s\n", 'POST', $uri, $name, $mw);
}
