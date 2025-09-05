<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "audit_logs table: ".(Schema::hasTable('audit_logs')?'OK':'MISSING').PHP_EOL;
if (!Schema::hasTable('audit_logs')) exit;

$data = [
  'user_id'        => null,
  'method'         => 'CLI',
  'route'          => '/scripts/sprint8_verify_php.php',
  'ip'             => null,
  'user_agent'     => 'cli',
  'payload_excerpt'=> 'test',
  'created_at'     => now(),
  'updated_at'     => now(),
];

if (Schema::hasColumn('audit_logs','role'))        $data['role'] = null;
if (Schema::hasColumn('audit_logs','action'))      $data['action'] = 'cli.test';
if (Schema::hasColumn('audit_logs','route_name'))  $data['route_name'] = 'scripts.verify';
if (Schema::hasColumn('audit_logs','path'))        $data['path'] = $data['route'];
if (Schema::hasColumn('audit_logs','payload'))     $data['payload'] = json_encode(['excerpt'=>$data['payload_excerpt']], JSON_UNESCAPED_UNICODE);
if (Schema::hasColumn('audit_logs','meta'))        $data['meta'] = json_encode(['env'=>'cli'], JSON_UNESCAPED_UNICODE);

DB::table('audit_logs')->insert($data);
$cnt = DB::table('audit_logs')->count();
echo "audit_logs count (after insert): $cnt\n";
