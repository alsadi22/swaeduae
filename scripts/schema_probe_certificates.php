<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema;
echo "certificates columns: ".(Schema::hasTable('certificates')? implode(', ', Schema::getColumnListing('certificates')) : '(missing)').PHP_EOL;
