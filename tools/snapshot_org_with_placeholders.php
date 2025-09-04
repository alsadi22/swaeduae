<?php
require 'vendor/autoload.php'; $app=require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

$u=\App\Models\User::where('email','support@swaeduae.ae')->first() ?: \App\Models\User::first();
if($u) Auth::login($u);
if(!View::shared('errors')) View::share('errors', new ViewErrorBag());

// placeholders for everything referenced by the blades
$vars = [
  'kpis'=>['volunteers'=>0,'events'=>0,'hours'=>0,'applications'=>0],
  'volunteersHosted'=>0,
  'totalHours'=>0,
  'upcomingOpps'=>0,
  'certificatesIssued'=>0,
  'appsTotal'=>0,
  'appsPending'=>0,
  'appsApproved'=>0,
  'appAttend'=>0,
  'checkinsToday'=>0,
  'hoursSeries'=>[0,0,0,0,0,0],
  'monthLabels'=>['Jan','Feb','Mar','Apr','May','Jun'],
  'recentActivity'=>[],
  'upcoming'=>[],
  'events'=>[],
  'latest_opps'=>[],
  'stats'=>[
    'active_volunteers'=>0,
    'total_opportunities'=>0,
    'pending_approvals'=>0,
    'total_hours'=>0,
  ],
];

@mkdir('storage/app/_orgdash_snaps',0777,true);
$html=view('org.dashboard',$vars)->render();
$file='storage/app/_orgdash_snaps/org_dashboard_PLACEHOLDER.html';
file_put_contents($file,$html);
echo "Wrote $file\n";
