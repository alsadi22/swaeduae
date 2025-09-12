<?php
require 'vendor/autoload.php'; $app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

$u = \App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($u) Auth::login($u);

$req = Request::create('/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
$res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
echo "HTTP ".$res->getStatusCode()." Location: ".$res->headers->get('Location').PHP_EOL;
