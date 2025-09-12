<?php
$dir = __DIR__.'/../routes';
$removed = 0;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
  if ($file->isDir() || $file->getExtension() !== 'php') continue;
  $p = $file->getPathname();
  $code = file_get_contents($p);
  $pat = "#Route::get\\(\\s*([\"'])/locale/\\{locale\\}\\1[\\s\\S]*?->name\\(\\s*([\"'])locale\\.switch\\2\\)\\s*;#m";
  if (preg_match($pat, $code)) {
    $new = preg_replace($pat, "/* removed duplicate /locale/{locale} route */", $code, -1, $n);
    if ($n) { copy($p, $p.'.bak_locale_dedupe'); file_put_contents($p, $new); echo "Removed duplicate in: $p\n"; $removed += $n; }
  }
}
echo $removed ? "Done. Removed $removed duplicate block(s).\n" : "No duplicate /locale/{locale} blocks found.\n";
