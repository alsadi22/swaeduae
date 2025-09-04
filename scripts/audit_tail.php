<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class); $k->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('audit_logs')->orderBy('id','desc')->limit(5)->get();
foreach ($rows as $r) {
  $action = $r->action ?? '-';
  $route  = $r->route ?? ($r->path ?? '-');
  echo "{$r->id}\t{$action}\t{$route}\t{$r->created_at}\n";
}
