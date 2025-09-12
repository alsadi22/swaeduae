<?php
$f='app/Providers/HealthScheduleServiceProvider.php';
$s=file_get_contents($f);
if (strpos($s,"queue:work --stop-when-empty")===false) {
  $s=str_replace(
    "->name('scheduler.heartbeat');",
    "->name('scheduler.heartbeat');\n\n        // Queue tick (shared hosting friendly)\n        \$schedule->command(\"queue:work --stop-when-empty --queue=default --tries=3 --backoff=10 --timeout=120 --sleep=3\")\n            ->everyMinute()->name('queue.tick');\n\n        // Restart workers daily (no-op for ticks, safe anyway)\n        \$schedule->command('queue:restart')->dailyAt('02:05');",
    $s, $c
  );
  if ($c) {
    copy($f,"$f.bak_".date('Ymd_His'));
    file_put_contents($f,$s);
    echo "[OK] queue scheduled\n";
  } else { echo "[WARN] could not patch; add manually under heartbeat.\n"; }
} else { echo "[SKIP] queue already scheduled\n"; }
