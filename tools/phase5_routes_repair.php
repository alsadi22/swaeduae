<?php
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator('routes', FilesystemIterator::SKIP_DOTS)
);
foreach ($it as $p) {
  if ($p->getExtension() !== 'php') continue;
  $path = $p->getPathname();
  if (strpos($path, '/_archived/') !== false) continue;

  $s = file_get_contents($path);
  $orig = $s;

  // Specific quick fix (guest case)
  $s = preg_replace(
    "/->middleware\\('guest'\\s*->middleware\\(/",
    "->middleware('guest')->middleware(",
    $s, -1, $c_guest
  );

  // Generic: if there is NO ')' just before '->middleware(' after a method call,
  // insert the missing ')'. This covers ->name('...'), ->middleware('...'), etc.
  $s = preg_replace(
    "/(->\\w+\\([^\\)]*)(?=->middleware\\()/",
    "$1)",
    $s, -1, $c_generic
  );

  if ($s !== $orig) {
    copy($path, $path . '.bak_fix_' . date('Ymd_His'));
    file_put_contents($path, $s);
    echo "[FIXED] $path (guest:$c_guest, generic:$c_generic)\n";
  }
}
