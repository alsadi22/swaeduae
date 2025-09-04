<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

$to = getenv('MONITOR_EMAIL') ?: ($argv[1] ?? null);
if(!$to){fwrite(STDERR,"Usage: php tools/mail_test.php admin@swaeduae.ae (or set MONITOR_EMAIL)\n"); exit(1);} 
Mail::raw('Test mail from swaeduae.ae @ '.now(), function($m) use($to){ $m->to($to)->subject('Mail test'); });
echo "[OK] test mail queued/sent to $to\n";
