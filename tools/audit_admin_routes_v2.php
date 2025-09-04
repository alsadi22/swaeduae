<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$routes = app('router')->getRoutes();
$all    = method_exists($routes,'getRoutes') ? $routes->getRoutes() : $routes;

printf("%-7s %-45s %-35s %s\n","METHOD","URI","NAME","MIDDLEWARE");
$found=false;
foreach ($all as $r) {
  $uri=$r->uri();
  if(!preg_match('#^admin(?:/|$)#',$uri)) continue;
  $found=true;
  printf("%-7s %-45s %-35s %s\n",
    implode('|',$r->methods()), $uri, $r->getName() ?? '-',
    implode(', ',$r->gatherMiddleware()));
}
if(!$found) echo "[INFO] No /admin routes matched.\n";
