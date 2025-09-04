<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema;
function line($k,$ok){ echo str_pad($k,34).': '.($ok?'OK':'MISSING').PHP_EOL; }
line('organizations table', Schema::hasTable('organizations'));
line('partner_intake_submissions table', Schema::hasTable('partner_intake_submissions'));
