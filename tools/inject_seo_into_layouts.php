<?php
$targets = [
  'resources/views/layouts/app.blade.php',
  'resources/views/layout.blade.php',
  'resources/views/app.blade.php',
  'resources/views/public/layout.blade.php',
  'resources/views/welcome.blade.php',
];
foreach ($targets as $f) {
  if (!file_exists($f)) continue;
  $s = file_get_contents($f);
  if (stripos($s, "components.seo") !== false) { echo "[SKIP] $f already has SEO\n"; continue; }
  if (preg_match('/<head[^>]*>/i', $s, $m, PREG_OFFSET_CAPTURE)) {
    $pos = $m[0][1] + strlen($m[0][0]);
    $inject = "\n    @include('components.seo')\n";
    $s2 = substr($s,0,$pos) . $inject . substr($s,$pos);
    copy($f, "$f.bak_".date('Ymd_His'));
    file_put_contents($f, $s2);
    echo "[OK] Injected SEO into $f\n";
  } else {
    echo "[WARN] <head> not found in $f\n";
  }
}
