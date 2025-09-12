<?php
$root = dirname(realpath(__DIR__.'/..'));
$ctr  = $root.'/app/Http/Controllers/EventController.php';
if (!is_file($ctr)) { fwrite(STDERR,"EventController not found\n"); exit(1); }
$src = file_get_contents($ctr); $orig = $src;

/* Only touch index() body. */
if (preg_match('/function\s+index\s*\([^)]*\)\s*\{(.+?)\n\}/s', $src, $m, PREG_OFFSET_CAPTURE)) {
  $body   = $m[1][0];
  $start  = $m[1][1];
  $length = strlen($body);
  $changed = false;

  // 1) Event::query() -> Event::query()->published()
  $new = preg_replace('/Event::query\(\)/', 'Event::query()->published()', $body, 1, $cnt1);
  if ($cnt1) { $body = $new; $changed = true; }

  // 2) First Event::method(  -> Event::published()->method(   (if not already published)
  $new = preg_replace('/Event::(?!published\()([a-zA-Z_][a-zA-Z0-9_]*)\(/', 'Event::published()->$1(', $body, 1, $cnt2);
  if ($cnt2) { $body = $new; $changed = true; }

  // 3) Fully-qualified \App\Models\Event::... -> ...published()->...
  $new = preg_replace('/\\\\?App\\\\Models\\\\Event::(?!published\()([a-zA-Z_][a-zA-Z0-9_]*)\(/', '\\App\\Models\\Event::published()->$1(', $body, 1, $cnt3);
  if ($cnt3) { $body = $new; $changed = true; }

  if ($changed) {
    $src = substr($src,0,$start).$body.substr($src,$start+$length);
  }
}

if ($src !== $orig) {
  copy($ctr, $ctr.'.fixbak_'.date('YmdHis'));
  file_put_contents($ctr,$src);
  echo "Patched: EventController@index uses Event::published()\n";
} else {
  echo "No changes needed\n";
}
