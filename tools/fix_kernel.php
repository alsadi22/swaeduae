<?php
// Normalize app/Http/Kernel.php: dedupe properties, remove non-alias Honeypot,
// ensure 'web' group has StartSession before VerifyCsrfToken, fix small typos.

$F = __DIR__ . '/../app/Http/Kernel.php';
$src = file_get_contents($F);
if ($src === false) { fwrite(STDERR, "Could not read Kernel.php\n"); exit(1); }

function removeDuplicatePropertyArrays(string $code, string $prop): array {
    $blocks = [];
    $needle = "protected \$$prop";
    $offset = 0;
    while (($p = strpos($code, $needle, $offset)) !== false) {
        // find start of array bracket after '='
        $eq = strpos($code, '=', $p);
        if ($eq === false) break;
        $lb = strpos($code, '[', $eq);
        if ($lb === false) break;
        // simple bracket depth scan until matching ];
        $i = $lb; $depth = 0; $end = null;
        while ($i < strlen($code)) {
            $ch = $code[$i];
            if ($ch === '[') $depth++;
            if ($ch === ']') { $depth--; if ($depth === 0) { $end = $i; break; } }
            $i++;
        }
        if ($end === null) break;
        // expect ; right after ]
        $semi = strpos($code, ';', $end);
        if ($semi === false) break;
        $blocks[] = [$p, $semi+1];
        $offset = $semi+1;
    }

    if (count($blocks) <= 1) return [$code, 0];

    // keep first, drop the rest by replacing with a comment
    for ($k=1; $k<count($blocks); $k++) {
        [$s, $e] = $blocks[$k];
        $len = $e - $s;
        $code = substr_replace($code, "\n// **** DUPLICATE \$$prop BLOCK REMOVED ****\n", $s, $len);
        // adjust subsequent indices
        $delta = strlen("\n// **** DUPLICATE \$$prop BLOCK REMOVED ****\n") - $len;
        for ($j=$k+1; $j<count($blocks); $j++) {
            $blocks[$j][0] += $delta; $blocks[$j][1] += $delta;
        }
    }
    return [$code, count($blocks)-1];
}

list($src, $rm1) = removeDuplicatePropertyArrays($src, 'routeMiddleware');
list($src, $rm2) = removeDuplicatePropertyArrays($src, 'middleware');
list($src, $rm3) = removeDuplicatePropertyArrays($src, 'middlewareGroups');

// Comment any non-alias usage of Honeypot (i.e., not "alias => Class")
$patterns = [
    '/^(\s*)(\\\\?App\\\\Http\\\\Middleware\\\\Honeypot::class,?\s*)$/mi',
    '/^(\s*)(\\\\?Spatie\\\\Honeypot\\\\ProtectAgainstSpam::class,?\s*)$/mi',
];
foreach ($patterns as $p) {
    $src = preg_replace_callback($p, function($m){
        // if the same line contains "=>", it is an alias mapping; keep it
        if (strpos($m[0], '=>') !== false) return $m[0];
        return $m[1] . '// removed: ' . trim($m[2]) . "\n";
    }, $src);
}

// Ensure 'web' group contains proper order: StartSession before VerifyCsrfToken, etc.
$src = preg_replace_callback(
    '/protected\s+\$middlewareGroups\s*=\s*\[(.*?)\];/si',
    function($m){
        $block = $m[0];
        // capture web group content
        if (!preg_match('/(\[["\']web["\']\]\s*=>\s*\[)(.*?)(\])/si', $block, $mm, PREG_OFFSET_CAPTURE)) {
            return $block;
        }
        $start = $mm[1][1];
        $inner = $mm[2][0];
        $endpos = $mm[3][1];

        // Remove any honeypot entries within web
        $inner = preg_replace('/^\s*(\\\\?App\\\\Http\\\\Middleware\\\\Honeypot::class|\\\\?Spatie\\\\Honeypot\\\\ProtectAgainstSpam::class),?\s*$/mi', '', $inner);

        $need = [
            '\\Illuminate\\Cookie\\Middleware\\EncryptCookies::class',
            '\\Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse::class',
            '\\Illuminate\\Session\\Middleware\\StartSession::class',
            '\\Illuminate\\View\\Middleware\\ShareErrorsFromSession::class',
            '\\App\\Http\\Middleware\\VerifyCsrfToken::class',
            '\\Illuminate\\Routing\\Middleware\\SubstituteBindings::class',
        ];

        // Extract FQCNs present
        preg_match_all('/([\\\\A-Za-z0-9_]+::class)/', $inner, $mm2);
        $present = array_unique($mm2[1] ?? []);

        // Build ordered list ensuring required ones in order; append any others at the end (dedup)
        $ordered = $need;
        foreach ($present as $fqcn) {
            if (!in_array($fqcn, $ordered, true)) $ordered[] = $fqcn;
        }

        $newInner = "            " . implode(",\n            ", $ordered) . ",\n        ";

        // stitch back
        $before = substr($block, 0, $start);
        $after  = substr($block, $endpos);
        return $before . $newInner . $after;
    },
    $src,
    1
);

// Fix any accidental bad token like App\Http\org → App\Http\Org
$src = str_replace('App\\Http\\org', 'App\\Http\\Org', $src);

file_put_contents($F, $src);

echo "Kernel normalized. Removed dup blocks: routeMiddleware=$rm1, middleware=$rm2, middlewareGroups=$rm3\n";
