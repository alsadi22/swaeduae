<?php
chdir(__DIR__ . '/..');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Route;

echo "=== Routes admin/org (resolved) ===\n";
foreach (Route::getRoutes() as $r) {
  $u=$r->uri();
  if (preg_match('#^(admin/login|admin/dashboard|org/login|org/dashboard)$#',$u)) {
    printf("%-8s /%-18s name=%-22s action=%s\n    mw=[%s]\n",
      implode('|',$r->methods()), $u, ($r->getName()??''), ($r->getActionName()??'closure'),
      implode(',', $r->gatherMiddleware()));
  }
}

echo "\n=== Files declaring /admin/login ===\n";
$rii=new RecursiveIteratorIterator(new RecursiveDirectoryIterator('routes'));
foreach($rii as $f){ if($f->isFile()&&str_ends_with($f->getFilename(),'.php')){
  $s=file_get_contents($f->getPathname());
  if(preg_match('/Route::(get|post|match)\s*\(\s*[\'"]\/admin\/login/i',$s))
    echo $f->getPathname(),"\n";
}}

echo "\n=== Non-alias Honeypot appearances ===\n";
exec('grep -RIn "Honeypot::class" routes app | grep -v -E "=>|\\.bak|_quarantine"', $out, $rc);
echo $out ? implode("\n",$out)."\n" : "None\n";
