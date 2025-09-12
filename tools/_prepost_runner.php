<?php
$outdir = $argv[1];
@mkdir($outdir, 0777, true);

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$summary = [];
$summary['now'] = date('c');
$summary['app_url'] = config('app.url');

$wantUris = ['login','org/login','dashboard','org/dashboard','profile'];
$summary['routes'] = [];
foreach (app('router')->getRoutes() as $r) {
  if (in_array($r->uri(), $wantUris, true)) {
    $summary['routes'][] = [
      'uri'     => $r->uri(),
      'name'    => $r->getName(),
      'action'  => $r->getActionName(),
      'methods' => $r->methods(),
    ];
  }
}

$u = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($u) { Auth::login($u); }

function render_to($path, $file) {
  $req = Request::create($path,'GET', [], [], [], ['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
  $res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
  $html = $res->getContent();
  @file_put_contents($file, $html);
  return [$res->getStatusCode(), $html, $res->headers->get('Location')];
}

[$codeLogin, $loginHtml] = render_to('/login',        "$outdir/login.html");
[$codeOrg,   $orgHtml]   = render_to('/org/dashboard',"$outdir/org_dashboard.html");
[$codeDash,  $_, $dashLoc]= render_to('/dashboard',   "$outdir/dashboard.html");
[$codeProf,  $_]         = render_to('/profile',      "$outdir/profile.html");

$summary['login_status']              = $codeLogin;
$summary['login_has_forgot']          = strpos($loginHtml, '/password/reset') !== false;
$summary['login_forgot_count']        = substr_count($loginHtml, '/password/reset');
$summary['login_has_raw_route_has']   = strpos($loginHtml, '(Route::has') !== false;

$summary['org_dashboard_status']      = $codeOrg;
$summary['org_dashboard_has_heading'] = strpos($orgHtml, 'Organization Dashboard') !== false;
if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>\s*My\s+Dashboard\s*<\/a>/i', $orgHtml, $m)) {
  $summary['org_nav_my_dashboard_href'] = $m[1];
}

$summary['dashboard_status']   = $codeDash;
$summary['dashboard_location'] = $dashLoc;

$v = view('org.dashboard');
$summary['org_dashboard_view_path'] = method_exists($v,'getPath') ? $v->getPath() : null;

try {
  $vv = view('auth.passwords.reset');
  $summary['reset_view_path'] = method_exists($vv,'getPath') ? $vv->getPath() : null;
} catch (Throwable $e) {
  $summary['reset_view_path']  = null;
  $summary['reset_view_error'] = $e->getMessage();
}

file_put_contents("$outdir/summary.json", json_encode($summary, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode($summary, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), PHP_EOL;
