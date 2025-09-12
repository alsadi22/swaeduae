<?php
$k = 'app/Http/Kernel.php';
if (!file_exists($k)) { fwrite(STDERR, "❌ $k not found\n"); exit(1); }
$src = file_get_contents($k);
$bak = $k.'.bak.'.time();
copy($k, $bak);

function inject($src, $property) {
  if (strpos($src, "Spatie\\Permission\\Middlewares\\RoleMiddleware") !== false) return $src; // already present
  $needle = "protected \$$property = [";
  $pos = strpos($src, $needle);
  if ($pos === false) return $src; // property not found
  $insert =
"        'role' => \\Spatie\\Permission\\Middlewares\\RoleMiddleware::class,\n".
"        'permission' => \\Spatie\\Permission\\Middlewares\\PermissionMiddleware::class,\n".
"        'role_or_permission' => \\Spatie\\Permission\\Middlewares\\RoleOrPermissionMiddleware::class,\n";
  return substr_replace($src, $needle."\n".$insert, $pos, strlen($needle));
}

if (strpos($src, 'protected $middlewareAliases = [') !== false) {
  $src2 = inject($src, 'middlewareAliases');
} else {
  // Fallback for older style; keep for safety
  $src2 = inject($src, 'routeMiddleware');
}

if ($src2 === $src) {
  echo "ℹ️ Nothing changed (aliases may already be present or property not found).\n";
} else {
  file_put_contents($k, $src2);
  echo "✅ Injected role/permission aliases into $k\nBackup: $bak\n";
}
