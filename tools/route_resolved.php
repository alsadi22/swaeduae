<?php
// Run from tools/, but work relative to project root:
chdir(__DIR__ . '/..');

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Effective routes for admin/org login & dashboard ===\n";
foreach (Route::getRoutes() as $r) {
  $u = $r->uri();
  if (in_array($u, ['admin/login','admin/dashboard','org/login','org/dashboard'])) {
    printf("%-10s /%-18s name=%-20s action=%s\n    mw=[%s]\n",
      implode('|',$r->methods()), $u, ($r->getName()??''), ($r->getActionName()??'closure'),
      implode(',', $r->gatherMiddleware())
    );
  }
}

echo "\n=== All route files that declare /admin/login (find dups) ===\n";
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('routes'));
foreach ($rii as $f) {
  if ($f->isFile() && str_ends_with($f->getFilename(), '.php') && !str_contains($f->getPathname(),'bak')) {
    $src = file_get_contents($f->getPathname());
    if (preg_match('/Route::(get|post|match)\s*\(\s*[\'"]\/admin\/login/i',$src)) {
      echo $f->getPathname(),"\n";
    }
  }
}

echo "\n=== Kernel: is Honeypot applied globally or to web group? ===\n";
$k = app(\App\Http\Kernel::class);
$ref = new ReflectionClass($k);
foreach (['middleware','middlewareGroups','routeMiddleware'] as $p) {
  $pr = $ref->getProperty($p); $pr->setAccessible(true);
  $val = $pr->getValue($k);
  echo strtoupper($p),":\n";
  if ($p==='middlewareGroups') {
    foreach ($val as $group=>$stack) {
      echo "  [$group]\n";
      foreach ($stack as $m) echo "    - $m\n";
    }
  } else {
    foreach ($val as $k2=>$m) {
      echo (is_int($k2) ? "  - $m" : "  $k2 => $m"), "\n";
    }
  }
}

echo "\n=== VerifyCsrfToken::$except ===\n";
$vt = new ReflectionClass(\App\Http\Middleware\VerifyCsrfToken::class);
$ex = $vt->getDefaultProperties()['except'] ?? [];
var_export($ex); echo "\n";

echo "\n=== Any auth:org / org:org still active in routes (excluding quarantine/backs) ===\n";
$hits = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('routes'));
foreach ($rii as $f) {
  if ($f->isFile() && str_ends_with($f->getFilename(), '.php')
      && !str_contains($f->getPathname(),'_quarantine') && !str_contains($f->getPathname(),'bak')) {
    $src = file_get_contents($f->getPathname());
    if (preg_match('/auth:org|org:org/',$src)) $hits[]=$f->getPathname();
  }
}
echo $hits ? implode("\n",$hits)."\n" : "None\n";
