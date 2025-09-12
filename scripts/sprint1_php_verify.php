<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$checks = [
  'applications (table)' => Schema::hasTable('applications'),
  'attendances (table)' => Schema::hasTable('attendances'),
  'opportunities.capacity' => Schema::hasColumn('opportunities','capacity'),
  'opportunities.waitlist_enabled' => Schema::hasColumn('opportunities','waitlist_enabled'),
  'volunteer_hours.minutes' => Schema::hasColumn('volunteer_hours','minutes'),
  'volunteer_hours.notes' => Schema::hasColumn('volunteer_hours','notes'),
  'volunteer_hours.source' => Schema::hasColumn('volunteer_hours','source'),
  'volunteer_hours.opportunity_id' => Schema::hasColumn('volunteer_hours','opportunity_id'),
];

foreach ($checks as $k => $ok) {
  echo str_pad($k, 40, ' ') . ': ' . ($ok ? 'OK' : 'MISSING') . PHP_EOL;
}
