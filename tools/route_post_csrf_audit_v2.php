<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

$router = app('router');
$routes = $router->getRoutes();

$write = ['POST','PUT','PATCH','DELETE'];
$csv = fopen(base_path('storage/route_post_csrf_audit.csv'), 'w');
fputcsv($csv, ['methods','uri','name','issues','middleware']);

$publicHp = [
  'login',
  'register',
  'org/register',
  'contact',
];

function hasCsrf($mw){ foreach($mw as $m) if(Str::contains($m,['web','VerifyCsrfToken'])) return true; return false; }
function hasHp($mw){ foreach($mw as $m) if(Str::contains($m,['honeypot','Honeypot'])) return true; return false; }
function hasThLogin($mw){ foreach($mw as $m) if(Str::startsWith($m,'throttle:login')) return true; return false; }

printf("%-8s %-40s %-35s %-30s %s\n", 'METHODS','URI','NAME','ISSUES','MIDDLEWARE');

foreach ($routes as $r) {
  $methods = $r->methods();
  if (!array_intersect($methods, $write)) continue;

  $uri = $r->uri(); $name = $r->getName() ?? '-'; $mw = $r->gatherMiddleware();

  if (Str::startsWith($uri,'api/')) continue; // API: no CSRF expected

  $issues = [];
  if (!hasCsrf($mw)) $issues[] = 'NO_CSRF';

  if (in_array($uri, $publicHp, true)) {
    if ($uri === 'login'    && !hasHp($mw)) $issues[] = 'NO_HONEYPOT';
    if ($uri === 'login'    && !hasThLogin($mw)) $issues[] = 'NO_THROTTLE_LOGIN';
    if ($uri === 'register' && !hasHp($mw)) $issues[] = 'NO_HONEYPOT';
    if ($uri === 'org/register' && !hasHp($mw)) $issues[] = 'NO_HONEYPOT';
    if ($uri === 'contact'  && !hasHp($mw)) $issues[] = 'NO_HONEYPOT';
  }

  $issuesStr = $issues ? implode('|',$issues) : '-';
  printf("%-8s %-40s %-35s %-30s %s\n",
    implode('|',$methods), $uri, $name, $issuesStr, implode(',', $mw));
  fputcsv($csv, [implode('|',$methods), $uri, $name, $issuesStr, implode(',', $mw)]);
}
fclose($csv);
echo "[OK] CSV written\n";
