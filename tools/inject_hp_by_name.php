<?php
$targets = glob(__DIR__.'/../routes/*.php') ?: [];
$needleRx = '#->name\s*\(\s*[\'"]org\.register\.submit[\'"]\s*\)#i';
$did = 0;

foreach ($targets as $f) {
    $s = file_get_contents($f);
    if ($s === false) continue;

    // Quick precheck: keep it fast
    if (!preg_match($needleRx, $s)) continue;

    // If Honeypot already present around this name, skip this file
    $around = preg_replace('/\s+/', ' ', $s);
    if (preg_match('#org\.register\.submit[\'"]\s*\)\s*;#i', $around)
        && preg_match('#Honeypot#i', $around)) {
        continue;
    }

    // Inject before ->name('org.register.submit') or ->name("org.register.submit")
    $patched = preg_replace(
        '#->name\s*\(\s*([\'"])org\.register\.submit\1\s*\)#i',
        '->middleware(\App\Http\Middleware\Honeypot::class)->name(\'org.register.submit\')',
        $s,
        1,
        $c
    );

    if ($c) {
        copy($f, $f.'.bak_'.date('Ymd_His'));
        file_put_contents($f, $patched);
        echo "[OK] Patched $f\n";
        $did++;
    }
}

echo $did ? "[DONE] Patched $did file(s)\n" : "[SKIP] Nothing to patch; not found or already present\n";
