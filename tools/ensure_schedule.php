<?php
$k="app/Console/Kernel.php"; $s=file_get_contents($k);
if (!preg_match('/protected function schedule\\(.*\\)\\s*\\{/', $s)) { fwrite(STDERR,"[ERR] schedule() not found\n"); exit(1); }
if (strpos($s,"sitemap:generate")===false) {
  $s=preg_replace('/(protected function schedule\\(.*\\)\\s*\\{)/s', "$1\n        \$schedule->command('sitemap:generate')->dailyAt('02:30');", $s,1);
}
if (strpos($s,"swaed:build-sitemaps")===false) {
  $s=preg_replace('/(protected function schedule\\(.*\\)\\s*\\{)/s', "$1\n        \$schedule->command('swaed:build-sitemaps')->dailyAt('02:35');", $s,1);
}
file_put_contents($k,$s);
echo "[OK] ensured schedule entries\n";
