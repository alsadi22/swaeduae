#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

echo "=== A) Route that serves the Organization Dashboard ==="
php -r '
require "vendor/autoload.php"; $app=require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = app("router")->getRoutes();
$r = $routes->getByName("org.dashboard");
if (!$r) {
  foreach ($routes as $x) { if ($x->uri()==="org/dashboard") { $r=$x; break; } }
}
if (!$r) { echo "NOT FOUND: route name=org.dashboard or uri=org/dashboard\n"; exit; }

$uses = $r->getAction()["uses"] ?? null;
echo "uri={$r->uri()} | name={$r->getName()} | methods=".implode(",",$r->methods()).PHP_EOL;
echo "action={$r->getActionName()}".PHP_EOL;
echo "middleware=".implode(",",$r->gatherMiddleware()).PHP_EOL;

if (is_string($uses) && strpos($uses,"@")) {
  [$cls,$m] = explode("@",$uses,2);
  $rm = new ReflectionMethod($cls,$m);
  echo "controller_file=".$rm->getFileName().":".$rm->getStartLine()."-".$rm->getEndLine().PHP_EOL;
}
'

echo
echo "=== B) Views that look like the org dashboard ==="
ls -la resources/views/org 2>/dev/null || true
grep -RIn --include='*.blade.php' -E 'route\([\"\x27]org\.dashboard[\"\x27]\)|Organization Dashboard|<h1[^>]*>.*Organization Dashboard' resources/views 2>/dev/null || true

echo
echo "=== C) Render /org/dashboard (server-side) and sanity-check the HTML ==="
php -r '
require "vendor/autoload.php"; $app=require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u=\App\Models\User::where("email","support@swaeduae.ae")->first() ?: \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($u);
$req = \Illuminate\Http\Request::create("/org/dashboard","GET",[],[],[],["HTTP_HOST"=>"swaeduae.ae","HTTPS"=>"on"]);
$res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
$h = $res->getContent();
echo "HTTP ".$res->getStatusCode()." | bytes=".strlen($h)." | has_heading=".(strpos($h,"Organization Dashboard")!==false?"yes":"no").PHP_EOL;
file_put_contents("storage/app/_org_dashboard_render.html",$h);
echo "Wrote: storage/app/_org_dashboard_render.html\n";
'

echo
echo "=== D) Places that still link to /profile instead of org.dashboard (just to see) ==="
grep -RIn --include='*.blade.php' -E 'href=\{\{[^}]*route\([\"\x27]profile[\"\x27]\)|href=\{\{[^}]*url\([\"\x27]/profile[\"\x27]\)' resources/views 2>/dev/null || true
