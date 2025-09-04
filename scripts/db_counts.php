<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = [
  'users','organizations','opportunities','applications','attendances',
  'volunteer_hours','certificates','partner_intake_submissions','events','audit_logs'
];

foreach ($tables as $t) {
  if (Schema::hasTable($t)) {
    $c = DB::table($t)->count();
    echo str_pad($t, 32, ' ', STR_PAD_RIGHT).": $c\n";
  } else {
    echo str_pad($t, 32, ' ', STR_PAD_RIGHT).": (MISSING)\n";
  }
}
