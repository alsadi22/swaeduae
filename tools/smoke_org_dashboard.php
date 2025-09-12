<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u=\App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if($u) \Illuminate\Support\Facades\Auth::login($u);
$r=app(\Illuminate\Contracts\Http\Kernel::class)->handle(
  \Illuminate\Http\Request::create('/org/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on'])
);
$h=$r->getContent();
echo "HTTP ".$r->getStatusCode()." | bytes=".strlen($h)." | title=".(strpos($h,'Organization Dashboard')!==false?'yes':'no').PHP_EOL;
