<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\DB;

function line($k,$ok){ echo str_pad($k,36,' ').': '.($ok?'OK':'MISSING').PHP_EOL; }
line('certificates (table)', Schema::hasTable('certificates'));
line('certificate_deliveries (table)', Schema::hasTable('certificate_deliveries'));
line('certificates.code', Schema::hasColumn('certificates','code'));
line('certificates.file_path', Schema::hasColumn('certificates','file_path'));
echo 'certificates rows: '.(Schema::hasTable('certificates')? DB::table('certificates')->count():0).PHP_EOL;
echo 'deliveries rows:   '.(Schema::hasTable('certificate_deliveries')? DB::table('certificate_deliveries')->count():0).PHP_EOL;
