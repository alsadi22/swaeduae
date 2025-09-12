<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('volunteer_hours')->where('opportunity_id',21)->get();
foreach ($rows as $r) echo "user_id={$r->user_id} minutes={$r->minutes} hours={$r->hours}\n";
