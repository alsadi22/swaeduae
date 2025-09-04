<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Http\Request;

@mkdir('storage/app/_orgdash_snaps', 0777, true);

// login as org
$u=\App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if ($u) Auth::login($u);

// avoid "Undefined variable $errors" from Argon flash includes (no code changes)
if (!View::shared('errors')) { View::share('errors', new ViewErrorBag()); }

$candidates = [
  'org.dashboard',
  'org.dashboard.index',
  'dashboard.organization',
  'dashboards.org',
  'organization.dashboard',
];

foreach ($candidates as $name) {
  $ok=true; $err=''; $html='';
  try {
    if (view()->exists($name)) {
      $req = Request::create('/org/dashboard','GET',[],[],[],['HTTP_HOST'=>'swaeduae.ae','HTTPS'=>'on']);
      $html = view($name)->render();
    } else { $ok=false; $err='view does not exist'; }
  } catch (\Throwable $e) { $ok=false; $err=$e->getMessage(); }
  $out='storage/app/_orgdash_snaps/'.str_replace(['.','/'],'_',$name).'.html';
  if ($ok) file_put_contents($out,$html);
  echo ($ok?'OK  ':'FAIL')." $name -> ".($ok?$out:$err).PHP_EOL;
}
