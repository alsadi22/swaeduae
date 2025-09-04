<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('opportunities')->select('id','title','region','start_date','end_date')->orderBy('id','desc')->limit(50)->get();
foreach ($rows as $r) {
  echo $r->id."\t".($r->title ?? '(untitled)')."\t".($r->region ?? '').PHP_EOL;
}
