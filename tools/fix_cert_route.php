<?php
$f = 'routes/web.php';
$c = file_get_contents($f);
if ($c === false) { fwrite(STDERR, "cannot read $f\n"); exit(1); }

$orig = $c;

/**
 * Remove any existing GET('/certificates', ...) route (named or not).
 * NOTE: This only targets the exact '/certificates' URI (not '/account/certificates').
 */
$pattern = "#Route::.*?get\\(\\s*['\\\"]/certificates['\\\"][\\s\\S]*?;\\s*#s";
$c = preg_replace($pattern, "", $c, -1, $removed);

/** Append canonical, verified route (FQCN so no 'use' needed) */
$canonical = "Route::middleware(['web','auth','verified'])->get('/certificates', [\\App\\Http\\Controllers\\CertificateController::class,'index'])->name('certificates.index');\n";
$c = rtrim($c)."\n\n".$canonical;

if ($c !== $orig) {
  file_put_contents($f, $c);
}
echo "Removed: {$removed} old /certificates route(s); appended canonical.\n";
