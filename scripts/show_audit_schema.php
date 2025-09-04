<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
if (!\Illuminate\Support\Facades\Schema::hasTable('audit_logs')) { echo "No audit_logs table.\n"; exit; }
$cols = DB::select('SHOW COLUMNS FROM audit_logs');
foreach ($cols as $c) echo $c->Field."\n";
