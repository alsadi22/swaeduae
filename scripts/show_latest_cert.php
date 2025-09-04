<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$c = DB::table('certificates')->orderBy('id','desc')->first();
if (!$c) { echo "No certificates found.\n"; exit; }
$base = url('/');
echo "Code: {$c->code}\n";
echo "Verify: {$base}/verify/{$c->code}\n";
echo "Download: {$base}/{$c->file_path}\n";
