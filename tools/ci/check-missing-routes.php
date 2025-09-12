<?php
$j = json_decode(shell_exec('php artisan route:list --json'), true);
$have = [];
foreach ($j as $r) if (!empty($r['name'])) $have[$r['name']] = 1;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
$bad = 0;
foreach ($rii as $f) {
  if (!preg_match('/\.blade\.php$/', $f)) continue;
  $ln = 0;
  foreach (file($f) as $line) {
    $ln++;
    if (preg_match_all("#route\\(['\"]([^'\"\\)]+)['\"]#", $line, $m)) {
      foreach ($m[1] as $n) {
        if (empty($have[$n])) { echo "$f:$ln: MISSING route('$n')\n"; $bad = 1; }
      }
    }
  }
}
exit($bad);
