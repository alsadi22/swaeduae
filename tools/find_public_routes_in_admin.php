<?php
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views/admin'));
$bad = [];
foreach ($it as $f) {
  if (!$f->isFile() || !preg_match('/\.blade\.php$/', $f->getFilename())) continue;
  $s = @file_get_contents($f->getPathname()) ?: '';
  // route('...')
  if (preg_match_all('/route\([\'"]([^\'"]+)[\'"]/', $s, $m)) {
    foreach (array_unique($m[1]) as $name) {
      if (strpos($name, 'admin.') !== 0) $bad[$name][] = $f->getPathname();
    }
  }
  // raw /opportunities, /events, /users links
  if (preg_match('/href="\/(opportunities|events|users)(\/|")/i', $s)) {
    $bad['HARD_CODED'][] = $f->getPathname();
  }
}
if (!$bad) { echo "(none)\n"; exit; }
foreach ($bad as $name => $files) {
  echo $name, " (", count($files), ")\n";
  foreach ($files as $ff) echo "  - $ff\n";
}
