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

  // route('destinationdetails', ...)  -> route('events.index')
  $new = preg_replace("/route\\(\\s*['\"]destinationdetails['\"][^)]*\\)/i",
                      "route('events.index')", $new);

  // url('/destinationdetails...')     -> url('/events')
  $new = preg_replace("/url\\(\\s*['\"]\\/?destinationdetails[^'\"\\)]*['\"]\\s*\\)/i",
                      "url('/events')", $new);

  // href="/destinationdetails..."     -> href="/events"
  $new = preg_replace("/href=([\"'])\\/?destinationdetails[^\"']*\\1/i",
                      "href='/events'", $new);

  if ($new !== $old) { file_put_contents($p, $new); echo "patched: $p\n"; $patched++; }
}
echo "done, patched=$patched\n";
