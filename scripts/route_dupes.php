<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class); $k->bootstrap();
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$map = [];
foreach ($routes as $r) {
  $n = $r->getName();
  if ($n) { $map[$n] = ($map[$n] ?? 0) + 1; }
}
foreach ($map as $n=>$c) if ($c>1) echo "$n x$c\n";
