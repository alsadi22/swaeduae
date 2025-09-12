<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

$u=\App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($u) Auth::login($u);

try {
  $req = Request::create('/org/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
  $res = app(\Illuminate\Contracts\Http\Kernel::class)->handle($req);
  echo "OK HTTP ".$res->getStatusCode()." (no exception)\n";
} catch (\Throwable $e) {
  echo "EXCEPTION: ".get_class($e)."\n".$e->getMessage()."\n";
  echo "FILE: ".$e->getFile().":".$e->getLine()."\n\n";
  // If it's a compiled Blade file, show context + original blade path marker
  $file = $e->getFile();
  if (strpos($file, 'storage/framework/views/') !== false && is_file($file)) {
    $lines = file($file);
    $L = max(1, $e->getLine()-8); $R = min(count($lines), $e->getLine()+8);
    for ($i=$L; $i<=$R; $i++) printf("%5d | %s", $i, $lines[$i-1]);
    echo "\n-- blade origin marker --\n";
    $tail = implode('', array_slice($lines, -20));
    if (preg_match('#/\*\*PATH\s+(.+?)\s+ENDPATH\*\*/#', $tail, $m)) {
      echo $m[1]."\n";
    }
  }
  if ($e->getPrevious()) {
    echo "\nCaused by: ".get_class($e->getPrevious())."\n".$e->getPrevious()->getMessage()."\n";
  }
}
