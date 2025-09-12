<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Laravel / App Info ===\n";
echo "APP_ENV=".config('app.env')."  APP_DEBUG=".var_export(config('app.debug'),true)."\n";
if(defined('\Illuminate\Foundation\Application::VERSION')) {
  echo "Laravel=". \Illuminate\Foundation\Application::VERSION ."\n";
}

echo "\n=== Session config (key bits) ===\n";
printf("driver=%s domain=%s secure=%s same_site=%s cookie=%s\n",
  config('session.driver'), json_encode(config('session.domain')),
  json_encode(config('session.secure')), json_encode(config('session.same_site')),
  config('session.cookie')
);

echo "\n=== Routes (admin/org: login & dashboard) — resolved middleware ===\n";
foreach (Route::getRoutes() as $r) {
  $u=$r->uri();
  if (preg_match('#^(admin/login|admin/dashboard|org/login|org/dashboard)$#',$u)) {
    printf("%-8s /%-18s name=%-22s action=%s\n    mw=[%s]\n",
      implode('|',$r->methods()), $u, ($r->getName()??''), ($r->getActionName()??'closure'),
      implode(',', $r->gatherMiddleware()));
  }
}

echo "\n=== Files declaring /admin/login (GET/POST) ===\n";
$rii=new RecursiveIteratorIterator(new RecursiveDirectoryIterator('routes'));
foreach($rii as $f){ if($f->isFile()&&str_ends_with($f->getFilename(),'.php')){
  $s=file_get_contents($f->getPathname());
  if(preg_match('/Route::(get|post|match)\s*\(\s*[\'"]\/admin\/login/i',$s))
    echo $f->getPathname(),"\n";
}}

echo "\n=== Kernel web group & middleware (first pass) ===\n";
$k = app(\App\Http\Kernel::class);
$ref = new ReflectionClass($k);
$pr = $ref->getProperty('middlewareGroups'); $pr->setAccessible(true);
$groups = $pr->getValue($k);
if (isset($groups['web'])) {
  echo "[web group]\n";
  foreach ($groups['web'] as $m) echo "  - $m\n";
} else {
  echo "No web group! (unexpected)\n";
}

echo "\n=== Controller constructor (AdminLoginController first lines) ===\n";
$path='app/Http/Controllers/Admin/Auth/AdminLoginController.php';
if (is_file($path)) {
  $lines = explode("\n", file_get_contents($path));
  for($i=0;$i<min(80,count($lines));$i++){ echo str_pad($i+1,3,' ',STR_PAD_LEFT).": ".$lines[$i]."\n"; }
} else { echo "Missing: $path\n"; }

echo "\n=== Non-alias Honeypot appearances ===\n";
exec('grep -RIn "Honeypot::class" routes app | grep -v -E "=>|\\.bak|_quarantine"', $out, $rc);
echo $out ? implode("\n",$out)."\n" : "None\n";
