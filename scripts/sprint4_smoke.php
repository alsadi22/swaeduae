<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('opportunities')) { echo "No opportunities table.\n"; exit; }
$rows = DB::table('opportunities')->orderBy('id','desc')->limit(5)->get();
foreach ($rows as $r) {
  $title = $r->title ?? '(untitled)';
  $region = $r->region ?? '-';
  echo "#{$r->id}  $title  [$region]\n";
}
