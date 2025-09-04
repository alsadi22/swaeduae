<?php
$f = 'routes/web.php';
$s = file_get_contents($f);
if ($s === false) { fwrite(STDERR,"[ERR] cannot read $f\n"); exit(1); }

$needle = "->name('org.register.submit')";
$pos = strpos($s, $needle);
if ($pos === false) { echo "[WARN] org.register.submit not found; patch manually.\n"; exit(0); }

// Find start of the Route::post(...) statement
$start = strrpos(substr($s,0,$pos), 'Route::post(');
if ($start === false) { echo "[WARN] Route::post(... name('org.register.submit')) not found; patch manually.\n"; exit(0); }

// Find end of the statement (next semicolon after name)
$end = strpos($s, ';', $pos);
if ($end === false) { echo "[WARN] could not find semicolon after route name.\n"; exit(0); }
$end++; // include ';'

// Extract the full statement and check if honeypot already present
$stmt = substr($s, $start, $end - $start);
if (stripos($stmt, 'honeypot') !== false || stripos($stmt, 'Honeypot') !== false) {
    echo "[SKIP] honeypot already present on org.register.submit\n"; exit(0);
}

// Inject honeypot just before ->name(
$patched = str_replace("->name('org.register.submit')",
    "->middleware(\\App\\Http\\Middleware\\Honeypot::class)->name('org.register.submit')",
    $stmt, $count);

if ($count < 1) { echo "[WARN] could not inject; patch manually.\n"; exit(0); }

// Write back
$before = substr($s, 0, $start);
$after  = substr($s, $end);
copy($f, "$f.bak_".date('Ymd_His'));
file_put_contents($f, $before.$patched.$after);
echo "[OK] honeypot added to org.register.submit in routes/web.php\n";
