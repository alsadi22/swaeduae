<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

$ok = true;

try { DB::select('select 1'); }
catch (\Throwable $e) { $ok = false; fwrite(STDERR, "DB FAIL: {$e->getMessage()}\n"); }

try {
    Cache::put('health_ping','1',5);
    if (Cache::get('health_ping') !== '1') throw new \RuntimeException('Cache mismatch');
} catch (\Throwable $e) { $ok = false; fwrite(STDERR, "CACHE FAIL: {$e->getMessage()}\n"); }

echo $ok ? "HEALTH OK\n" : "HEALTH FAIL\n";
exit($ok ? 0 : 1);
