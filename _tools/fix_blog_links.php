<?php
$views = __DIR__ . '/../resources/views';
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS)
);
$patched = 0;
foreach ($it as $f) {
  $p = $f->getPathname();
  if (!preg_match('/\.blade\.php$/', $p)) continue;
  $old = file_get_contents($p); $new = $old;

  // route('blogdetails', ... )  -> route('events.index')
  $new = preg_replace("/route\\(\\s*['\"]blogdetails['\"][^)]*\\)/i",
                      "route('events.index')", $new);

  // url('/blogdetails...')      -> url('/events')
  $new = preg_replace("/url\\(\\s*['\"]\\/?blogdetails[^'\"\\)]*['\"]\\s*\\)/i",
                      "url('/events')", $new);

  // href="/blogdetails..."      -> href="/events"
  $new = preg_replace("/href=([\"'])\\/?blogdetails[^\"']*\\1/i",
                      "href='/events'", $new);

  if ($new !== $old) { file_put_contents($p, $new); echo "patched: $p\n"; $patched++; }
}
echo "done, patched=$patched\n";
