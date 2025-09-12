<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
  'users',
  'events',
  'opportunities',
  'settings',
  'site_settings',
  'kyc',
  'certificates',
  'volunteer_profiles',
  'volunteer_hours',
];

$out = [];
foreach ($tables as $t) {
  try {
    $cols = Schema::hasTable($t) ? Schema::getColumnListing($t) : [];
    $out[$t] = $cols;
  } catch (Throwable $e) {
    $out[$t] = ['<error: '.$e->getMessage().'>'];
  }
}
file_put_contents('SWAED_SCHEMA_AUDIT.txt', print_r($out, true));
echo "Wrote SWAED_SCHEMA_AUDIT.txt\n";
