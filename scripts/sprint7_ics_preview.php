<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function firstCol($table,$cands){ foreach($cands as $c) if (\Illuminate\Support\Facades\Schema::hasColumn($table,$c)) return $c; return null; }
function rangeCols($t){ return [firstCol($t,['start_date','event_date','start_at','date','created_at']), firstCol($t,['end_date','end_at','finish_date'])]; }
$events = [];
if (Schema::hasTable('opportunities')) {
  [$s,$e]=rangeCols('opportunities');
  if ($s) {
    $rows = DB::table('opportunities')->orderBy($s,'asc')->limit(3)->get();
    foreach($rows as $r){ $events[] = ($r->title ?? 'Opportunity')." — ".($r->$s ?? $r->created_at); }
  }
}
echo "Sample upcoming:\n".implode("\n",$events)."\n";
