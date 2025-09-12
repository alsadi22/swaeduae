<?php
$path = 'app/Http/Controllers/Org/OpportunityController.php';
$code = file_get_contents($path);
if ($code === false) { fwrite(STDERR, "Cannot read $path\n"); exit(1); }

$pos = strpos($code, 'function orgFromRequest');
if ($pos === false) { fwrite(STDERR, "orgFromRequest() not found in $path\n"); exit(1); }

// find start of function block
$brace = strpos($code, '{', $pos);
if ($brace === false) { fwrite(STDERR, "Function brace not found\n"); exit(1); }

// walk braces to find end of function
$level = 0; $i = $brace;
for (; $i < strlen($code); $i++) {
    if ($code[$i] === '{') $level++;
    if ($code[$i] === '}') { $level--; if ($level === 0) { $end = $i+1; break; } }
}
if (!isset($end)) { fwrite(STDERR, "Failed to locate function end\n"); exit(1); }

$new = <<<'FN'
protected function orgFromRequest(\Illuminate\Http\Request $request)
{
    $u = $request->user();
    if (!$u) { return null; }

    // Detect which ownership column exists in `organizations`
    $cols = \Illuminate\Support\Facades\Schema::getColumnListing('organizations');
    $q = \Illuminate\Support\Facades\DB::table('organizations');

    $added = false;
    foreach (['owner_user_id','owner_id','user_id'] as $c) {
        if (in_array($c, $cols, true)) {
            // Use OR to match any of the valid ownership columns
            $q->orWhere($c, $u->id);
            $added = true;
        }
    }

    if (!$added) {
        // Fallback: no known column, return null to avoid SQL error
        return null;
    }

    return $q->first();
}

FN;

$patched = substr($code, 0, $pos) . $new . substr($code, $end);
file_put_contents($path, $patched);
echo "Patched orgFromRequest() in $path\n";
