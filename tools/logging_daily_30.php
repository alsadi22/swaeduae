<?php
// 1) Force LOG_CHANNEL=daily in .env (idempotent)
$env = file_exists('.env') ? file_get_contents('.env') : '';
if (preg_match('/^LOG_CHANNEL=/m', $env)) {
  $env = preg_replace('/^LOG_CHANNEL=.*/m', 'LOG_CHANNEL=daily', $env);
} else {
  $env .= (substr($env, -1) === "\n" ? "" : "\n") . "LOG_CHANNEL=daily\n";
}
file_put_contents('.env', $env);
echo "[OK] .env: LOG_CHANNEL=daily\n";

// 2) Set daily retention to 30 days in config/logging.php
$f = 'config/logging.php';
$s = file_get_contents($f);
$pat = '/([\'"]daily[\'"]\s*=>\s*\[(?:.|\R)*?[\'"]days[\'"]\s*=>\s*)\d+/m';
$patched = preg_replace($pat, '$1'.'30', $s, 1, $c);
if ($c) { copy($f, "$f.bak_".date('Ymd_His')); file_put_contents($f,$patched); echo "[OK] daily.days=30\n"; }
else { echo "[SKIP] Could not patch daily.days (already 30 or layout different)\n"; }
