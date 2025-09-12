<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$db  = DB::getDatabaseName();
$pdo = DB::connection()->getPdo();

$audPath = base_path('storage/db_index_audit.json');
if (!file_exists($audPath)) { fwrite(STDERR,"[ERR] audit JSON missing\n"); exit(1); }
$aud = json_decode(file_get_contents($audPath), true) ?: [];

$total = 0; $skipped = 0; $applied = 0; $dups = 0; $errors = 0;

foreach ($aud as $table => $issues) {
  foreach ($issues as $row) {
    [$type,$col,$sql] = $row;
    $total++;

    // If UNIQUE is desired, check for duplicates first
    if ($type === 'want_unique') {
      $q = $pdo->prepare("SELECT `{$col}`, COUNT(*) c FROM `{$table}` GROUP BY `{$col}` HAVING `{$col}` IS NOT NULL AND c>1 LIMIT 5");
      try {
        $q->execute(); $dupRows = $q->fetchAll(PDO::FETCH_ASSOC);
      } catch (Throwable $e) {
        fwrite(STDERR,"[WARN] dup-check failed {$table}.{$col}: ".$e->getMessage()."\n");
        $dupRows = [];
      }
      if ($dupRows && count($dupRows)) {
        $dups++;
        echo "[SKIP] {$table}.{$col} UNIQUE — duplicates exist (top shown): ".json_encode($dupRows)."\n";
        continue;
      }
    }

    if ($apply) {
      try {
        $pdo->exec($sql);
        $applied++;
        echo "[OK] applied: {$sql}\n";
      } catch (Throwable $e) {
        $errors++;
        echo "[ERR] {$table}.{$col}: ".$e->getMessage()."\nSQL: {$sql}\n";
      }
    } else {
      echo "[DRY] would apply: {$sql}\n";
    }
  }
}
echo "[SUMMARY] total:$total applied:$applied skipped:$skipped dupBlocks:$dups errors:$errors mode:".($apply?'APPLY':'DRY')."\n";
