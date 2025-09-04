<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$router = app('router');
$targets = ['login','register','admin/login'];

printf("%-8s %-18s %-35s %s\n", 'METHOD', 'URI', 'NAME', 'MIDDLEWARE');
foreach ($router->getRoutes() as $route) {
    $uri = $route->uri();
    if (!in_array($uri, $targets, true)) continue;
    if (!in_array('POST', $route->methods(), true)) continue;

    $name = $route->getName() ?? '-';
    $mw   = implode(', ', $route->gatherMiddleware());
    printf("%-8s %-18s %-35s %s\n", 'POST', $uri, $name, $mw);
}
