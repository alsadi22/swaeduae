<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

$u = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if($u) Auth::login($u);

$req = Request::create('/org/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
$res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
$html = $res->getContent();

$expected = URL::route('org.dashboard', [], true);
preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/is', $html, $A, PREG_SET_ORDER);

$found = null; $dashboardTextLinks = [];
foreach ($A as $m) {
  $href = html_entity_decode($m[1], ENT_QUOTES);
  $text = trim(strip_tags($m[2]));
  if (stripos($href, '/org/dashboard') !== false || stripos($href, $expected) !== false) { $found = $href; break; }
  if (preg_match('/\bDashboard\b/i', $text)) $dashboardTextLinks[] = $text.' | '.$href;
}

echo "HTTP ".$res->getStatusCode()."\n";
echo "Expected org.dashboard URL: ".$expected."\n";
echo "Found org-dashboard href: ".($found ?: "(not found)")."\n";
if (!$found) {
  echo "Other anchors containing 'Dashboard':\n";
  foreach (array_slice($dashboardTextLinks,0,10) as $x) echo "  - $x\n";
}
