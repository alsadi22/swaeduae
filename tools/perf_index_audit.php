<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$want = [
  'opportunity_applications' => [
    ['cols'=>['user_id'],        'unique'=>false, 'name'=>'opp_apps_user_id_idx'],
    ['cols'=>['opportunity_id'], 'unique'=>false, 'name'=>'opp_apps_opp_id_idx'],
  ],
  'volunteer_hours' => [
    ['cols'=>['user_id'],        'unique'=>false, 'name'=>'vol_hours_user_id_idx'],
  ],
  'certificates' => [
    ['cols'=>['user_id'],        'unique'=>false, 'name'=>'certs_user_id_idx'],
  ],
  'organizations' => [
    ['cols'=>['owner_user_id'],  'unique'=>false, 'name'=>'orgs_owner_user_id_idx'],
  ],
  'settings' => [
    ['cols'=>['key'],            'unique'=>true,  'name'=>'settings_key_unique'],
  ],
];

$schema = DB::selectOne("SELECT DATABASE() AS db")->db;
$have = [];
foreach (array_keys($want) as $table) {
    $rows = DB::select("
        SELECT index_name, non_unique, seq_in_index, column_name
        FROM information_schema.statistics
        WHERE table_schema = ? AND table_name = ?
        ORDER BY index_name, seq_in_index
    ", [$schema, $table]);
    foreach ($rows as $r) {
        $have[$table][$r->index_name]['non_unique'] = (int) $r->non_unique;
        $have[$table][$r->index_name]['cols'][]     = $r->column_name;
    }
}

$missing = [];
foreach ($want as $table => $indexes) {
    foreach ($indexes as $idx) {
        $colsWanted   = implode(',', $idx['cols']);
        $uniqueWanted = (bool) $idx['unique'];
        $matched = false;
        foreach (($have[$table] ?? []) as $iname => $idata) {
            $colsHave   = implode(',', $idata['cols'] ?? []);
            $uniqueHave = isset($idata['non_unique']) ? !$idata['non_unique'] : false;
            if ($colsHave === $colsWanted && $uniqueHave === $uniqueWanted) {
                $matched = true; break;
            }
        }
        if (!$matched) $missing[] = [
            'table'=>$table, 'name'=>$idx['name'],
            'unique'=>$uniqueWanted, 'cols'=>$idx['cols']
        ];
    }
}

if (!$missing) { echo "OK: no indexes missing\n"; exit(0); }

echo "Missing indexes:\n";
foreach ($missing as $m) {
    echo "- {$m['table']} [".($m['unique']?'UNIQUE ':'').implode(',',$m['cols'])."] as {$m['name']}\n";
}

if (in_array('--apply=1', $argv, true)) {
    foreach ($missing as $m) {
        $sql = sprintf(
            "ALTER TABLE `%s` ADD %s INDEX `%s` (%s)",
            $m['table'],
            $m['unique'] ? 'UNIQUE' : '',
            $m['name'],
            implode(',', array_map(fn($c)=>"`$c`", $m['cols']))
        );
        DB::statement($sql);
        echo "APPLIED: $sql\n";
    }
}
