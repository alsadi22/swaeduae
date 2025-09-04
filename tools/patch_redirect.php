<?php
$f = __DIR__ . '/../app/Http/Controllers/Auth/AuthenticatedSessionController.php';
if (!file_exists($f)) { exit(0); }
$c = file_get_contents($f);
if (strpos($c, 'redirect()->intended') !== false && strpos($c, '/org') === false) {
  $c = preg_replace(
    '#return\s+redirect\(\)->intended\([^)]+\);#',
    'return ($request->input("type") === "organization") ? redirect()->intended("/org") : redirect()->intended("/dashboard");',
    $c, 1
  );
  file_put_contents($f, $c);
  echo "Patched AuthenticatedSessionController redirect.\n";
}
