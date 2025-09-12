<?php
$views = __DIR__ . '/../resources/views';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS));
$patched = 0;

function repl($s, $a){ return preg_replace(array_keys($a), array_values($a), $s); }

$RENAME = [
  // header/footer/navbar & general public
  "/route\\(\\s*['\"]pages\\.faq['\"][^)]*\\)/i"              => "route('faq')",
  "/route\\(\\s*['\"]login\\.volunteer['\"][^)]*\\)/i"        => "route('login')",

  // public collections
  "/route\\(\\s*['\"]public\\.events['\"][^)]*\\)/i"          => "route('events.index')",
  "/route\\(\\s*['\"]public\\.opportunities['\"][^)]*\\)/i"   => "route('opportunities.index')",
  "/route\\(\\s*['\"]public\\.organizations['\"][^)]*\\)/i"   => "route('partners')",

  // public detail pages
  "/route\\(\\s*['\"]public\\.opportunities\\.show['\"]/i"    => "route('opps.public.show'",
  "/route\\(\\s*['\"]public\\.opportunity\\.show['\"]/i"      => "route('opps.public.show'",
  "/route\\(\\s*['\"]opportunities\\.show['\"]/i"             => "route('opps.public.show'",

  // scan form (POST)
  "#route\\(\\s*['\"]scan['\"]\\s*\\)#i"                      => "route('scan.submit')",

  // gallery fallback (no route in app)
  "/route\\(\\s*['\"]public\\.gallery['\"][^)]*\\)/i"         => "url('/')",
];

foreach ($it as $f) {
  $p = $f->getPathname();
  if (!preg_match('/\\.blade\\.php$/', $p)) continue;
  $old = file_get_contents($p);
  $new = repl($old, $RENAME);

  if ($new !== null && $new !== $old) {
    file_put_contents($p, $new);
    echo "patched: $p\n";
    $patched++;
  }
}
echo "done, patched=$patched\n";
