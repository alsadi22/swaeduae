<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

function line($k,$v){ printf("%-28s %s\n",$k.':',$v); }

$orgUser = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($orgUser) Auth::login($orgUser);

// Which Blade is actually used?
$view = view('org.dashboard');
$viewPath = method_exists($view,'getPath') ? $view->getPath() : '(unknown)';
line('view("org.dashboard")', $viewPath);

// One-level includes from the main dashboard blade
$includes = [];
if (is_file($viewPath)) {
  $src = file_get_contents($viewPath);
  if (preg_match_all("/@include\(['\"]([^'\"]+)['\"]\)/", $src, $m)) {
    foreach ($m[1] as $inc) {
      $f = 'resources/views/'.str_replace('.','/',$inc).'.blade.php';
      if (is_file($f)) $includes[] = $f;
    }
  }
}
line('1-level includes found', count($includes));
foreach ($includes as $f) line(' -', $f);

// Hit the route and capture output
$req = Request::create('/org/dashboard','GET',[],[],[],[
  'HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on'
]);
$start = microtime(true);
try {
  $res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
  $html = $res->getContent();
  $ms = round((microtime(true)-$start)*1000);
  line('HTTP Status', $res->getStatusCode());
  line('Render time', $ms.' ms');
  line('HTML size', strlen($html).' bytes');

  // Quick layout sanity (very light-weight string checks)
  $checks = [
    'Argon CSS linked'           => 'vendor/argon/assets/css/argon-dashboard.min.css',
    'Side nav likely present'    => 'g-sidenav-show',
    'Heading present'            => 'Organization Dashboard',
    'KPI: CERTIFICATES ISSUED'   => 'CERTIFICATES ISSUED',
    'KPI: UPCOMING OPPORTUNITIES'=> 'UPCOMING OPPORTUNITIES',
    'KPI: TOTAL HOURS'           => 'TOTAL HOURS',
    'Block: Applications vs Attendance' => 'Applications vs Attendance',
    'Block: Volunteer Hours'     => 'Volunteer Hours',
  ];

  $fail = 0;
  echo "\nChecks:\n";
  foreach ($checks as $label => $needle) {
    $ok = (stripos($html,$needle)!==false);
    printf("  [%s] %s\n", $ok?'OK':'MISS', $label);
    if(!$ok) $fail++;
  }

  // Show a short hint if anything looks off
  if ($fail) {
    echo "\nHint: If Argon assets/partials are missing, re-open resources/views/org/layout.blade.php and verify:\n";
    echo "  @includeIf('org.argon._sidenav')\n";
    echo "  @includeIf('admin.argon._navbar')\n";
    echo "  @includeIf('org.partials.menu')\n";
    echo "  @includeIf('admin.argon._footer')\n";
  }

} catch (\Throwable $e) {
  line('HTTP Status','(threw before response)');
  echo "\nEXCEPTION ".get_class($e).": ".$e->getMessage()."\n";
  echo "FILE ".$e->getFile().":".$e->getLine()."\n";
  // If it’s a compiled blade, show context + origin
  $file = $e->getFile();
  if (strpos($file,'storage/framework/views/')!==false && is_file($file)) {
    $lines = file($file);
    $L = max(1, $e->getLine()-8); $R = min(count($lines), $e->getLine()+8);
    echo "\n-- compiled context --\n";
    for ($i=$L; $i<=$R; $i++) printf("%5d | %s", $i, $lines[$i-1]);
    $tail = implode('', array_slice($lines, -20));
    if (preg_match('#/\*\*PATH\s+(.+?)\s+ENDPATH\*\*/#', $tail, $m)) {
      echo "\n-- blade origin --\n".$m[1]."\n";
    }
  }
}
