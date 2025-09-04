<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Mail;
Mail::raw('SwaedUAE SMTP test: '.date('c'), function ($m) {
  $m->to(env('MAIL_FROM_ADDRESS','info@swaeduae.ae'))
    ->subject('SMTP test from server');
});
echo "sent\n";
