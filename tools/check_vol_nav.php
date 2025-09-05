<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

$vol = \App\Models\User::whereDoesntHave('roles', fn($q)=>$q->where('name','org'))->first() ?: \App\Models\User::first();
if($vol){ Auth::login($vol); }

$res = app(\Illuminate\Contracts\Http\Kernel::class)
  ->handle(Request::create('/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']));
echo "VOL /dashboard -> HTTP {$res->getStatusCode()} Location: ".$res->headers->get('Location').PHP_EOL;
