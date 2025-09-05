<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$c = config('database.connections.mysql');
$map = ['host'=>'DB_HOST','port'=>'DB_PORT','database'=>'DB_NAME','username'=>'DB_USER','password'=>'DB_PASS','unix_socket'=>'DB_SOCKET'];
foreach ($map as $k=>$env) {
  $v = $c[$k] ?? '';
  $v = str_replace(['\\','"'], ['\\\\','\"'], $v);
  echo "$env=\"$v\"\n";
}
