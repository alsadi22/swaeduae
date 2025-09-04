<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('certificates')->orderBy('id','desc')->limit(10)->get();
foreach ($rows as $r) { echo "{$r->id}\t{$r->code}\t/{$r->file_path}\n"; }
