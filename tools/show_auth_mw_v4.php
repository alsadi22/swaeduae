<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

function check(string $uri, string $method = 'POST') {
    $router = app('router');
    $req = Request::create('/'.$uri, $method);
    try {
        $route = $router->getRoutes()->match($req);
        $name  = $route->getName() ?? '-';
        $mw    = implode(', ', $route->gatherMiddleware());
        printf("%-6s %-20s %-40s %s\n", $method, $uri, $name, $mw);
    } catch (\Throwable $e) {
        printf("%-6s %-20s %-40s %s\n", $method, $uri, '-', 'NO MATCH: '.$e->getMessage());
    }
}

printf("%-6s %-20s %-40s %s\n", 'METHOD','URI','NAME','MIDDLEWARE');
check('login', 'POST');
check('register', 'POST');
check('admin/login', 'POST');
