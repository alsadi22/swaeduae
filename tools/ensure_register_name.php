<?php
$dir = __DIR__.'/../routes';
$updated = 0;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
  if ($file->isDir() || $file->getExtension() !== 'php') continue;
  $p = $file->getPathname();
  $c = file_get_contents($p);
  // Only touch blocks that POST /register and do not already have ->name('register.perform')
  if (preg_match("#Route::post\\(\\s*([\"'])/register\\1[\\s\\S]*?;#m", $c, $m, PREG_OFFSET_CAPTURE)) {
    $block = $m[0][0];
    if (strpos($block, "name('register.perform')") === false && strpos($block, 'name("register.perform")') === false) {
      $block2 = preg_replace("#;\\s*$#", "->name('register.perform');", $block, 1);
      $c = substr_replace($c, $block2, $m[0][1], strlen($block));
      copy($p, $p.'.bak_regname'); file_put_contents($p, $c); echo "Named register.perform in: $p\n"; $updated++;
    }
  }
}
echo $updated ? "Updated $updated file(s).\n" : "No unnamed POST /register blocks found.\n";
