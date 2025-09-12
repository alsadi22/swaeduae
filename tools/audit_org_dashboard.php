<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Pretty printer
function row($k,$ok,$hint=''){
  $status = $ok ? 'OK ' : 'MISS';
  printf("  [%s] %-35s %s\n", $status, $k, $ok ? '' : $hint);
}

// Pick any org user
$orgUser = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($orgUser) Auth::login($orgUser);

// Render the page through HTTP kernel (no CDN)
$req = Request::create('/org/dashboard','GET',[],[],[],[
  'HTTP_HOST'=>'swaeduae.ae', 'HTTPS'=>'on'
]);
try {
  $t0 = microtime(true);
  $res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
  $html = $res->getContent();
  $ms   = round((microtime(true)-$t0)*1000);
  echo "HTTP: ".$res->getStatusCode()." | ".$ms." ms | ".strlen($html)." bytes\n\n";
} catch (\Throwable $e) {
  echo "EXCEPTION: ".$e->getMessage()."\n"; exit(1);
}

// Checks (strings, not brittle DOM)
$checks = [
  'Argon CSS linked'          => 'vendor/argon/assets/css/argon-dashboard.min.css',
  'Argon JS linked'           => 'vendor/argon/assets/js/argon-dashboard.min.js',
  'Sidenav include footprint' => 'g-sidenav-show',
  'Heading'                   => 'Organization Dashboard',
  'Action pills: Settings'    => 'Settings',
  'Action pills: KYC / License'=> 'KYC / License',
  'Action pills: Team'        => 'Team',
  'Action pills: Applicants'  => 'Applicants',
  'Action pills: Dashboard'   => 'Dashboard',

  // KPI cards
  'KPI Certificates'          => 'CERTIFICATES ISSUED',
  'KPI Upcoming'              => 'UPCOMING OPPORTUNITIES',
  'KPI Hours'                 => 'TOTAL HOURS CONTRIBUTED',
  'KPI Volunteers'            => 'TOTAL VOLUNTEERS',

  // Main graphs
  'Block Apps vs Attendance'  => 'Applications vs Attendance',
  'Block Volunteer Hours'     => 'Volunteer Hours',
  'Canvas appsAttendChart'    => 'id="appsAttendChart"',
  'Canvas hoursChart'         => 'id="hoursChart"',

  // Side blocks
  'Recent activity'           => 'Recent activity',
  'Upcoming (7 days)'         => 'Upcoming (7 days)',
  'Today check-ins'           => 'Today check-ins',
];

echo "Checks:\n";
$miss = 0;
foreach ($checks as $label=>$needle) {
  $ok = (stripos($html,$needle)!==false);
  row($label,$ok);
  if(!$ok) $miss++;
}

// Simple structure sanity
$cards = preg_match_all('/class="[^"]*\bcard\b/i',$html);
row('Card elements >= 8', $cards>=8, "found=$cards");

// Layout includes on source files
$layout = 'resources/views/org/layout.blade.php';
$needles = [
  "@includeIf('org.argon._sidenav')",
  "@includeIf('admin.argon._navbar')",
  "@includeIf('org.partials.menu')",
  "@includeIf('admin.argon._footer')",
];
if (is_file($layout)) {
  $src = file_get_contents($layout);
  foreach ($needles as $n) row("Layout has $n", strpos($src,$n)!==false);
}

echo "\nSummary: ".($miss? "FAIL ($miss missing)" : "PASS")."\n";
exit($miss? 2:0);
