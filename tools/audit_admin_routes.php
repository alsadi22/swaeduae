<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$router = app('router');
printf("%-7s %-45s %-35s %s\n","METHOD","URI","NAME","MIDDLEWARE");
$found=false;
foreach ($router->getRoutes() as $r) {
    $uri=$r->uri();
    if (strpos($uri,'admin/')!==0) continue;
    $found=true;
    $name=$r->getName() ?? '-';
    $mw=implode(', ',$r->gatherMiddleware());
    printf("%-7s %-45s %-35s %s\n", implode('|',$r->methods()), $uri, $name, $mw);
}
if(!$found){ echo "[INFO] No /admin/* routes found.\n"; }
