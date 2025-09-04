<?php
$path = 'app/Http/Controllers/Org/OpportunityController.php';
$src  = file_get_contents($path);
$start = strpos($src, 'protected function statusValueForClose');
$end   = strpos($src, 'public function close(', $start);
if ($start === false || $end === false) { fwrite(STDERR, "Signature not found\n"); exit(1); }
$before = substr($src, 0, $start);
$after  = substr($src, $end);

$method = <<<'CODE'
protected function statusValueForClose(): mixed
{
    $meta = \Illuminate\Support\Facades\DB::selectOne("
        SELECT DATA_TYPE data_type, COLUMN_TYPE column_type
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME='events'
          AND COLUMN_NAME='status'
        LIMIT 1
    ");

    if (!$meta) return 'closed';
    $t  = strtolower($meta->data_type ?? '');
    $ct = strtolower($meta->column_type ?? '');

    if ($t === 'enum' && $ct) {
        $matches = [];
        if (preg_match_all("/'([^']+)'/", $ct, $matches)) {
            if (in_array('closed', $matches[1] ?? [], true)) return 'closed';
            return $matches[1][0] ?? 'closed';
        }
        return 'closed';
    }

    // If status is numeric, use 0 for "closed"
    if (in_array($t, ['tinyint','smallint','int','integer','bigint','mediumint','bit'], true)) {
        return 0;
    }

    return 'closed';
}

CODE;

file_put_contents($path, $before.$method.$after);
echo "Patched statusValueForClose() in $path\n";
