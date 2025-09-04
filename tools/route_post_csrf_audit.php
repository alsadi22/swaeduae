<?php
// tools/route_post_csrf_audit.php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

$router = app('router');
$routes = $router->getRoutes();

$writeMethods = ['POST','PUT','PATCH','DELETE'];
$csvPath = base_path('storage/route_post_csrf_audit.csv');
$csv = fopen($csvPath, 'w');
fputcsv($csv, ['methods','uri','name','issues','middleware']);

function hasCsrf($mw) {
    foreach ($mw as $m) if (Str::contains($m, ['web', 'VerifyCsrfToken'])) return true;
    return false;
}
function hasHoneypot($mw) {
    foreach ($mw as $m) if (Str::contains($m, ['honeypot', 'Honeypot'])) return true;
    return false;
}
function hasThrottleLogin($mw) {
    foreach ($mw as $m) if (Str::startsWith($m, 'throttle:login')) return true;
    return false;
}

printf("%-8s %-40s %-35s %-30s %s\n", 'METHODS','URI','NAME','ISSUES','MIDDLEWARE');

foreach ($routes as $r) {
    $methods = $r->methods();
    if (!array_intersect($methods, $writeMethods)) continue;

    $uri  = $r->uri();
    $name = $r->getName() ?? '-';
    $mw   = $r->gatherMiddleware();

    // Skip API routes (no CSRF expected)
    if (Str::startsWith($uri, 'api/')) continue;

    $issues = [];
    if (!hasCsrf($mw)) $issues[] = 'NO_CSRF';

    // Auth form extras
    if (preg_match('#(^|/)login$#', $uri)) {
        if (!hasHoneypot($mw))      $issues[] = 'NO_HONEYPOT';
        if (!hasThrottleLogin($mw)) $issues[] = 'NO_THROTTLE_LOGIN';
    }
    if (preg_match('#(^|/)register$#', $uri)) {
        if (!hasHoneypot($mw))      $issues[] = 'NO_HONEYPOT';
    }
    if (preg_match('#(^|/)contact$#', $uri)) {
        if (!hasHoneypot($mw))      $issues[] = 'NO_HONEYPOT';
    }

    $issuesStr = $issues ? implode('|', $issues) : '-';
    printf("%-8s %-40s %-35s %-30s %s\n",
        implode('|', $methods), $uri, $name, $issuesStr, implode(',', $mw));

    fputcsv($csv, [
        implode('|', $methods),
        $uri,
        $name,
        $issuesStr,
        implode(',', $mw),
    ]);
}
fclose($csv);

echo "[OK] CSV written: {$csvPath}\n";
