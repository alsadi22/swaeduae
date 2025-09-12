<?php
$root = dirname(realpath(__DIR__.'/..'));
$ctr  = $root.'/app/Http/Controllers/EventController.php';
if (!is_file($ctr)) { fwrite(STDERR,"EventController not found\n"); exit(1); }
$src = file_get_contents($ctr); $orig = $src;

/**
 * Replace the first "$events = Event::something(...)" with
 * "$events = Event::published()->something(...)" if not already present.
 * Also handle "\App\Models\Event::..." form.
 */
$re1 = '/(\$\w+\s*=\s*Event::)(?!published\()\s*/m';
$re2 = '/(\$\w+\s*=\s*\\\\?App\\\\Models\\\\Event::)(?!published\()\s*/m';
$src = preg_replace($re1, '$1published()->', $src, 1);
$src = preg_replace($re2, '$1published()->', $src, 1);

if ($src !== $orig) {
  copy($ctr, $ctr.'.bak_'.date('YmdHis'));
  file_put_contents($ctr,$src);
  echo "Patched: EventController uses Event::published()\n";
} else {
  echo "No changes needed (already published() or pattern not found)\n";
}
