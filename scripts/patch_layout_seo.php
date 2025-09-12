<?php
$f = __DIR__.'/../resources/views/layouts/app.blade.php';
if (!file_exists($f)) { echo "[SEO] layouts/app.blade.php not found, skipping.\n"; exit; }
$txt = file_get_contents($f);
if (strpos($txt, "partials.seo") !== false) { echo "[SEO] Meta include already present.\n"; exit; }
$bak = $f.'.'.date('Ymd_His').'.bak';
copy($f, $bak);
if (preg_match('/<\/head>/i', $txt)) {
  $txt = preg_replace('/<\/head>/i', "    @includeIf('partials.seo')\n</head>", $txt, 1);
  file_put_contents($f, $txt);
  echo "[SEO] Injected @includeIf('partials.seo') before </head>. Backup: $bak\n";
} else {
  file_put_contents($f, "@includeIf('partials.seo')\n".$txt);
  echo "[SEO] No </head> found; prepended include. Backup: $bak\n";
}
