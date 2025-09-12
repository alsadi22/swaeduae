<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Auth; use Illuminate\Support\Facades\Config; use Illuminate\Http\Request;
Config::set('app.debug', true);
$u=\App\Models\User::where('email','support@swaeduae.ae')->first()?:\App\Models\User::first(); if($u) Auth::login($u);
@mkdir('storage/app/_orgdash_snaps',0777,true);
$res = app(\Illuminate\Contracts\Http\Kernel::class)->handle(Request::create('/org/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']));
$file='storage/app/_orgdash_snaps/last_500.html'; file_put_contents($file,$res->getContent());
echo "HTTP {$res->getStatusCode()} saved: $file\n";
