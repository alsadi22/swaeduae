<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

[$script,$email,$opId,$minutes] = $argv + [null,null,null,0];
$opId = (int)$opId; $minutes = (int)$minutes;

if (!$email || !$opId) { fwrite(STDERR,"Usage: php scripts/seed_hours.php EMAIL OPPORTUNITY_ID MINUTES\n"); exit(1); }

$user = DB::table('users')->where('email',$email)->first();
if (!$user) { fwrite(STDERR,"User not found: $email\n"); exit(1); }

$eventId = null;
if (Schema::hasColumn('volunteer_hours','event_id')) {
  $cand = null;
  if (Schema::hasColumn('opportunities','event_id')) {
    $cand = DB::table('opportunities')->where('id',$opId)->value('event_id');
  }
  if ($cand && Schema::hasTable('events')) {
    $exists = DB::table('events')->where('id',$cand)->exists();
    if ($exists) $eventId = (int)$cand;
  }
  // else leave NULL (allowed)
}

$where = ['user_id'=>$user->id, 'opportunity_id'=>$opId];
$update = [
  'minutes'=>$minutes,
  'notes'=>'seed via script',
  'source'=>'seed',
  'updated_at'=>now(),
  'created_at'=>now()
];
if ($eventId !== null) $update['event_id'] = $eventId;
if (Schema::hasColumn('volunteer_hours','hours')) $update['hours'] = (int) floor($minutes/60);

DB::table('volunteer_hours')->updateOrInsert($where, $update);
echo "OK: set $minutes minutes for {$email} on opportunity {$opId}"
   .($eventId!==null?" (event_id=$eventId)":" (event_id=NULL)")
   .(Schema::hasColumn('volunteer_hours','hours')?" (hours=".(int)floor($minutes/60).")":"")
   ."\n";
