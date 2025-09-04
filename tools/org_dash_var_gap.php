<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$main = 'resources/views/org/dashboard.blade.php';
$partials = [$main];
if (preg_match_all("/@include\(['\"]([^'\"]+)['\"]\)/",$mainSrc=file_get_contents($main),$m)) {
  foreach ($m[1] as $inc) {
    $p = 'resources/views/'.str_replace('.','/',$inc).'.blade.php';
    if (is_file($p)) $partials[]=$p;
  }
}
$p2='resources/views/org/partials/dashboard_v1.blade.php';
if (is_file($p2)) $partials[]=$p2;

$need = [];
foreach ($partials as $f) {
  preg_match_all('/\$[A-Za-z_][A-Za-z0-9_]*/', file_get_contents($f), $vm);
  foreach ($vm[0] as $v) $need[$v]=true;
}
unset($need['$errors'],$need['$loop'],$need['$__data'],$need['$__path'],$need['$attributes'],$need['$message']);
$need = array_keys($need); sort($need);

$routes=app('router')->getRoutes(); $r=$routes->getByName('org.dashboard');
[$cls,$m]=explode('@',$r->getActionName(),2);
$code=file_get_contents((new ReflectionMethod($cls,$m))->getFileName());
$have=[];
if (preg_match('/return\s+view\(\s*[\'"]org\.dashboard[\'"]\s*,\s*\[([\s\S]*?)\]\s*\)/',$code,$mm)) {
  if (preg_match_all('/[\'"]([A-Za-z_][A-Za-z0-9_]*)[\'"]\s*=>/',$mm[1],$km)) $have=$km[1];
}
$needClean=array_map(fn($x)=>ltrim($x,'$'),$need);
$missing = array_values(array_diff($needClean,$have));
echo "Missing vars (blade needs but controller not passing):\n";
foreach ($missing as $x) echo " - $x\n";
