<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u=\App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($u);
$r=app(\Illuminate\Contracts\Http\Kernel::class)->handle(\Illuminate\Http\Request::create('/org/dashboard','GET'));
$h=$r->getContent();
if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>\s*My\s+Dashboard\s*<\/a>/i',$h,$m)) {
  echo "My Dashboard href: ",$m[1],PHP_EOL;
} else {
  echo "Could not find a My Dashboard anchor.\n";
}
