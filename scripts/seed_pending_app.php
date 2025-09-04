<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$uid = DB::table('users')->value('id') ?: DB::table('users')->insertGetId([
  'name'=>'Volunteer Tester','email'=>'vol+'.time().'@swaeduae.ae','password'=>bcrypt(Str::random(16)),
  'is_admin'=>0,'is_active'=>1,'created_at'=>now(),'updated_at'=>now(),
]);
$eid = DB::table('events')->value('id') ?: DB::table('events')->insertGetId([
  'title'=>'Demo Event','description'=>'Seeded','date'=>date('Y-m-d', strtotime('+5 days')),
  'location'=>'Dubai','hours'=>2,'created_at'=>now(),'updated_at'=>now(),
]);
DB::table('event_volunteer')->updateOrInsert(
  ['event_id'=>$eid,'user_id'=>$uid],
  ['status'=>'pending','applied_at'=>now(),'created_at'=>now(),'updated_at'=>now()]
);
echo "Seeded pending: user=$uid event=$eid\n";
