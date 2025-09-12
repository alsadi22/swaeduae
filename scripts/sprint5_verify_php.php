<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema;
function line($k,$ok){ echo str_pad($k,28).': '.($ok?'OK':'MISSING').PHP_EOL; }
line('certificates table', Schema::hasTable('certificates'));
line('volunteer_hours table', Schema::hasTable('volunteer_hours'));
line('applications table', Schema::hasTable('applications'));
