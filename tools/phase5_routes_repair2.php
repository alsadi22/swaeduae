<?php
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator('routes', FilesystemIterator::SKIP_DOTS)
);
foreach ($it as $p) {
  if ($p->getExtension() !== 'php') continue;
  $path = $p->getPathname();
  if (strpos($path, '/_archived/') !== false) continue;

  $s = file_get_contents($path); $orig = $s;

  // Specific trims for our inserted middlewares
  $s = preg_replace(
    "/->middleware\\(\\s*\\['honeypot','throttle:login'\\]\\s*\\)\\)\\s*;/",
    "->middleware(['honeypot','throttle:login']);",
    $s, -1, $c1
  );
  $s = preg_replace(
    "/->middleware\\(\\s*\\['honeypot'\\]\\s*\\)\\)\\s*;/",
    "->middleware(['honeypot']);",
    $s, -1, $c2
  );

  // Generic: collapse any '...->middleware(...));' to '...->middleware(...);'
  $s = preg_replace(
    "/(->middleware\\([^;]*?\\))\\)\\s*;/",
    "$1;",
    $s, -1, $c3
  );

  if ($s !== $orig) {
    copy($path, $path . '.bak_fix2_' . date('Ymd_His'));
    file_put_contents($path, $s);
    echo "[FIXED] $path (specific:$c1+$c2 generic:$c3)\n";
  }
}
