<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
$rows = \Illuminate\Support\Facades\DB::select('SHOW FULL COLUMNS FROM certificates');
foreach ($rows as $r) {
  $need = ($r->Null === 'NO' && $r->Default === null) ? 'REQUIRED' : '';
  echo str_pad($r->Field,24).' | Null='.$r->Null.' | Default='.($r->Default===null?'NULL':$r->Default).' '.$need.PHP_EOL;
}
