<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
if (!Schema::hasTable('organizations')) { echo "No organizations table.\n"; exit; }
$rows = DB::table('organizations')->select('id','name')->orderBy('id','desc')->limit(10)->get();
foreach ($rows as $r) echo "#{$r->id}\t".($r->name ?? '(unnamed)').PHP_EOL;
