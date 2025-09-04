<?php
$k = "app/Console/Kernel.php";
$s = file_get_contents($k);
if ($s === false) { fwrite(STDERR,"[ERR] cannot read $k\n"); exit(1); }

if (!preg_match('/protected\s+function\s+schedule\s*\(.*?\)\s*(?::\s*void)?\s*\{/s', $s)) {
  fwrite(STDERR,"[ERR] schedule() not found\n"); exit(1);
}

$changed = false;
if (strpos($s, "sitemap:generate") === false) {
  $s = preg_replace('/(protected\s+function\s+schedule\s*\(.*?\)\s*(?::\s*void)?\s*\{)/s',
      "$1\n        \$schedule->command('sitemap:generate')->dailyAt('02:30');",
      $s, 1, $c1);
  $changed = $changed || ($c1 ?? 0) > 0;
}
if (strpos($s, "swaed:build-sitemaps") === false) {
  $s = preg_replace('/(protected\s+function\s+schedule\s*\(.*?\)\s*(?::\s*void)?\s*\{)/s',
      "$1\n        \$schedule->command('swaed:build-sitemaps')->dailyAt('02:35');",
      $s, 1, $c2);
  $changed = $changed || ($c2 ?? 0) > 0;
}

if ($changed) {
  copy($k, "$k.bak_".date('Ymd_His'));
  file_put_contents($k, $s);
  echo "[OK] schedule updated\n";
} else {
  echo "[SKIP] schedule already contains sitemap jobs\n";
}
