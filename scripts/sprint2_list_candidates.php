<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
if (!\Illuminate\Support\Facades\Schema::hasTable('volunteer_hours')) { echo "No volunteer_hours.\n"; exit; }
$rows = DB::table('volunteer_hours as h')
  ->select('h.opportunity_id', DB::raw('COUNT(*) as volunteers'), DB::raw('SUM(COALESCE(h.minutes,0)) as minutes'))
  ->where('h.minutes','>',0)
  ->groupBy('h.opportunity_id')->orderByDesc('minutes')->limit(20)->get();
foreach ($rows as $r) { echo "Opportunity ID: {$r->opportunity_id} — Volunteers: {$r->volunteers} — Minutes: {$r->minutes}\n"; }
