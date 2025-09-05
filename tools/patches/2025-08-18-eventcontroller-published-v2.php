<?php
$root = dirname(realpath(__DIR__.'/..'));
$ctr  = $root.'/app/Http/Controllers/EventController.php';
if (!is_file($ctr)) { fwrite(STDERR,"EventController not found\n"); exit(1); }
$src = file_get_contents($ctr); $orig = $src;
/* Add published() after Event:: if not present */
$src = preg_replace('/(\\$\\w+\\s*=\\s*\\\\?App\\\\\\\\Models\\\\\\\\Event::)(?!published\\()/', '$1published()->', $src, 1);
$src = preg_replace('/(\\$\\w+\\s*=\\s*Event::)(?!published\\()/', '$1published()->', $src, 1);
/* Write only if changed */
if ($src !== $orig) {
  copy($ctr, $ctr.'.bak_'.date('YmdHis'));
  file_put_contents($ctr, $src);
  echo "Patched: EventController uses Event::published()\n";
} else {
  echo "No changes needed (either already published() or pattern not matched)\n";
}
