<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (!Schema::hasTable('opportunities')) { echo "No opportunities table.\n"; exit; }
$cols = Schema::getColumnListing('opportunities');

$idCol = 'id';
$titleCol = in_array('title',$cols) ? 'title' : (in_array('name',$cols) ? 'name' : null);
$regionCol = in_array('region',$cols) ? 'region' : (in_array('emirate',$cols) ? 'emirate' : (in_array('city',$cols) ? 'city' : null));
$dateStartCol = in_array('start_date',$cols) ? 'start_date' : (in_array('start_at',$cols) ? 'start_at' : (in_array('date',$cols) ? 'date' : null));
$dateEndCol = in_array('end_date',$cols) ? 'end_date' : (in_array('end_at',$cols) ? 'end_at' : null);

$select = [$idCol];
foreach ([$titleCol,$regionCol,$dateStartCol,$dateEndCol,'created_at'] as $c) { if ($c && in_array($c,$cols)) $select[] = $c; }

$rows = DB::table('opportunities')->select($select)->orderBy($idCol,'desc')->limit(50)->get();

echo "Columns: ".implode(', ',$select).PHP_EOL;
foreach ($rows as $r) {
  $line = "ID: ".$r->$idCol;
  if ($titleCol) $line .= " | Title: ".($r->$titleCol ?? '(untitled)');
  if ($regionCol) $line .= " | Region: ".($r->$regionCol ?? '');
  if ($dateStartCol) $line .= " | Start: ".($r->$dateStartCol ?? '');
  if ($dateEndCol) $line .= " | End: ".($r->$dateEndCol ?? '');
  echo $line.PHP_EOL;
}
