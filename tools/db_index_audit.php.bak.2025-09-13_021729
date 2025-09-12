<?php
// tools/db_index_audit.php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$db  = DB::getDatabaseName();
$pdo = DB::connection()->getPdo();

function fetchAll($pdo, $sql, $p){
    $st=$pdo->prepare($sql); $st->execute($p); return $st->fetchAll(PDO::FETCH_ASSOC);
}

$tables = fetchAll($pdo, "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=?", [$db]);

$outDir   = base_path('storage');
@mkdir($outDir, 0777, true);
$sqlFile  = $outDir.'/db_index_suggestions.sql';
$jsonFile = $outDir.'/db_index_audit.json';

$suggestions = [];
$audit = [];

foreach ($tables as $tRow) {
    $t = $tRow['TABLE_NAME'];

    $cols = fetchAll($pdo,
        "SELECT COLUMN_NAME, COLUMN_KEY, DATA_TYPE
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA=? AND TABLE_NAME=?
         ORDER BY ORDINAL_POSITION", [$db, $t]);

    $idxs = fetchAll($pdo,
        "SELECT INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME
         FROM INFORMATION_SCHEMA.STATISTICS
         WHERE TABLE_SCHEMA=? AND TABLE_NAME=?
         ORDER BY INDEX_NAME, SEQ_IN_INDEX", [$db, $t]);

    // index presence map
    $indexed = [];
    foreach ($idxs as $ix) { $indexed[$ix['COLUMN_NAME']] = true; }

    // unique single-column map
    $byIndex = [];
    foreach ($idxs as $ix) { $byIndex[$ix['INDEX_NAME']][] = $ix; }
    $uniqueSingle = [];
    foreach ($byIndex as $name => $colsInIx) {
        $unique = count($colsInIx) === 1 && intval($colsInIx[0]['NON_UNIQUE']) === 0;
        if ($unique) $uniqueSingle[$colsInIx[0]['COLUMN_NAME']] = true;
    }

    $colNames = array_column($cols, 'COLUMN_NAME');
    $colSet   = array_flip($colNames);
    $issues   = [];

    // Rule A: *_id should be indexed
    foreach ($colNames as $c) {
        if (str_ends_with($c, '_id') && empty($indexed[$c])) {
            $issues[] = ["missing_index", $c, "CREATE INDEX idx_{$t}_{$c} ON `{$t}` (`{$c}`);"];
        }
    }

    // Rule B: common unique fields
    $wantUnique = [];
    if (isset($colSet['email']) && $t === 'users') $wantUnique[] = 'email';
    if (isset($colSet['slug']))                    $wantUnique[] = 'slug';
    if (isset($colSet['uuid']))                    $wantUnique[] = 'uuid';
    if (isset($colSet['code']) && preg_match('/certificat|verify|verification/i',$t)) $wantUnique[] = 'code';

    foreach ($wantUnique as $c) {
        if (empty($uniqueSingle[$c])) {
            $issues[] = ["want_unique", $c, "ALTER TABLE `{$t}` ADD UNIQUE `{$t}_{$c}_unique` (`{$c}`);"];
        }
    }

    // Rule C: created_at nice-to-have
    if (isset($colSet['created_at']) && empty($indexed['created_at'])) {
        $issues[] = ["nice_to_have", 'created_at', "CREATE INDEX idx_{$t}_created_at ON `{$t}` (`created_at`);"];
    }

    // Rule D: token columns should be indexed
    foreach (['token','remember_token','reset_token'] as $c) {
        if (isset($colSet[$c]) && empty($indexed[$c])) {
            $issues[] = ["missing_index", $c, "CREATE INDEX idx_{$t}_{$c} ON `{$t}` (`{$c}`);"];
        }
    }

    if ($issues) {
        $audit[$t] = $issues;
        foreach ($issues as $row) {
            $suggestions[] = "-- {$t}.{$row[0]}: {$row[1]}\n{$row[2]}\n";
        }
    }
}

file_put_contents($sqlFile, implode("\n", $suggestions));
file_put_contents($jsonFile, json_encode($audit, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

echo $suggestions ? "[OK] Suggestions written to {$sqlFile}\n" : "[OK] No index suggestions detected.\n";
echo "[OK] Audit JSON: {$jsonFile}\n";
