<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

echo "== Route that serves org dashboard ==\n";
$r = app('router')->getRoutes()->getByName('org.dashboard');
if (!$r) { foreach (app('router')->getRoutes() as $x) if ($x->uri()==='org/dashboard') { $r=$x; break; } }
echo $r ? "uri={$r->uri()} | action={$r->getActionName()} | mw=".implode(',',$r->gatherMiddleware())."\n" : "NOT FOUND\n";

echo "\n== Current view bound to view('org.dashboard') ==\n";
$v  = view('org.dashboard');
$fp = method_exists($v,'getPath') ? $v->getPath() : '(unknown)';
echo "path: $fp\n";
if (is_file($fp)) {
  $sz = filesize($fp);
  echo "size: {$sz} bytes\n";
}

echo "\n== Known candidates (by convention) ==\n";
$cands = [
  'org.dashboard',
  'org.dashboard.index',
  'dashboard.organization',
  'dashboards.org',
  'organization.dashboard',
];
$found = [];
foreach ($cands as $name) {
  if (view()->exists($name)) {
    $vv=view($name);
    $p = method_exists($vv,'getPath') ? $vv->getPath() : '(unknown)';
    $s = is_file($p) ? filesize($p) : 0;
    $found[] = [$name,$p,$s];
  }
}
usort($found, fn($a,$b)=>$b[2]<=>$a[2]);
foreach ($found as [$name,$p,$s]) echo sprintf("%-26s | %7d bytes | %s\n",$name,$s,$p);

echo "\n== Any backup blades that look like your old page ==\n";
$bak = glob('resources/views/org/dashboard.blade.php.bak_*') ?: [];
usort($bak, fn($a,$b)=>filemtime($b)<=>filemtime($a));
foreach ($bak as $b) echo date('Y-m-d H:i:s', filemtime($b))." | ".filesize($b)." bytes | $b\n";

echo "\n== Snapshot render (org user) of each candidate (no changes) ==\n";
@mkdir('storage/app/_orgdash_snaps', 0777, true);
$org = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($org) Auth::login($org);
foreach ($found as [$name,$p,$s]) {
  $ok=true; $err='';
  try {
    $html = view($name)->render();
  } catch (\Throwable $e) { $ok=false; $err=$e->getMessage(); $html=''; }
  $out = 'storage/app/_orgdash_snaps/'.str_replace(['.','/'],'_',$name).'.html';
  if ($ok) { file_put_contents($out,$html); }
  echo ($ok?'OK  ':'FAIL')." $name -> ".($ok?$out:$err)."\n";
}

echo "\n== Grep for heading 'Organization Dashboard' (quick content clue) ==\n";
$cmd = "grep -RIn --include='*.blade.php' -E 'Organization Dashboard' resources/views 2>/dev/null";
passthru($cmd);
