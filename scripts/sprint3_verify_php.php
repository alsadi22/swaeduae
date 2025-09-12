<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
use Illuminate\Support\Facades\Schema;

echo "contact_messages table: ".(Schema::hasTable('contact_messages')?'OK':'MISSING').PHP_EOL;
foreach (['name','email','subject','message','ip','user_agent','locale','is_bot'] as $c) {
  echo " - $c: ".(Schema::hasColumn('contact_messages',$c)?'OK':'MISSING').PHP_EOL;
}
