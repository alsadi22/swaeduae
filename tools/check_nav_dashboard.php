<?php
require 'vendor/autoload.php'; $app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/** login as an org user you have; fall back to first user */
$u = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
Auth::login($u);

/** render a page that includes the navbar (org dashboard is fine) */
$req = Request::create('/org/dashboard','GET', [], [], [], ['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
$res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
$html = $res->getContent();

/** find the “My Dashboard” link */
if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>\s*My\s+Dashboard\s*<\/a>/i', $html, $m)) {
  echo "My Dashboard href: {$m[1]}\n";
} else {
  echo "My Dashboard anchor not found (try searching for generic Dashboard links)\n";
}

/** also show where /dashboard redirects to (server-side) */
$r2 = app(\Illuminate\Contracts\Http\Kernel::class)
  ->handle(Request::create('/dashboard','GET', [], [], [], ['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']));
echo "GET /dashboard -> HTTP {$r2->getStatusCode()} Location: ".$r2->headers->get('Location')."\n";
