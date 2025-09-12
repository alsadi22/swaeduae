<?php
require __DIR__."/../vendor/autoload.php";
$app = require __DIR__."/../bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$known = array_flip(array_keys(app('router')->getRoutes()->getRoutesByName()));

$views = __DIR__ . '/../resources/views';
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS)
);

$bad = [];
foreach ($it as $f) {
  $path = $f->getPathname();
  if (!preg_match('/\.blade\.php$/', $path)) continue;

  // Phase-2 scan scope: skip admin/org & migration dump templates
  if (preg_match('#/resources/views/(admin|org)/#', $path)) continue;
  if (preg_match('#/resources/views/_from_migrations_#', $path)) continue;

  $lines = file($path);
  foreach ($lines as $i => $line) {
    // Ignore $request->route('token') or anything using ->route('x')
    if (preg_match_all("/(?<!->)route\\(\\s*['\"]([a-zA-Z0-9._-]+)['\"]/i", $line, $m)) {
      foreach ($m[1] as $name) {
        if (!isset($known[$name])) $bad[] = [$path, $i+1, $name, trim($line)];
      }
    }
  }
}

if (!$bad) { echo "OK: no undefined route() names found in Blade files.\n"; exit; }
echo "Undefined route() names found:\n";
foreach ($bad as [$p,$ln,$nm,$snip]) echo "- $nm @ $p:$ln :: $snip\n";
