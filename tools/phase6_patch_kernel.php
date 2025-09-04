<?php
$f='app/Console/Kernel.php';
$c=file_get_contents($f);
$needle = "tools/backup.sh";
if (strpos($c,$needle)===false) {
  $ins = "        // Nightly backup\n        \$schedule->exec('bash tools/backup.sh')->dailyAt('03:10');\n";
  $c = preg_replace('/protected\s+function\s+schedule\(Schedule\s+\$schedule\):\s*void\s*\{\s*/', "$0\n$ins", $c, 1);
  file_put_contents($f,$c);
  echo "Kernel scheduled backup\n";
} else {
  echo "Kernel already has backup schedule\n";
}
