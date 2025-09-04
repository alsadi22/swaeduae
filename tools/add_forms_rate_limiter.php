<?php
$f='app/Providers/RouteServiceProvider.php';
$s=file_get_contents($f);

if(strpos($s,'forms',)===false){
  // ensure use RateLimiter + Request
  if(!preg_match('/use\s+Illuminate\\\Cache\\\RateLimiting\\\RateLimiter;/', $s)){
    $s=preg_replace('/namespace\s+App\\\Providers;(\s*)/','namespace App\\\Providers;$1use Illuminate\\\Cache\\\RateLimiting\\\RateLimiter;',$s,1);
  }
  if(!preg_match('/use\s+Illuminate\\\Http\\\Request;/', $s)){
    $s=preg_replace('/use\s+Illuminate\\\Cache\\\RateLimiting\\\RateLimiter;(\s*)/','$0'."use Illuminate\\\Http\\\Request;\n",$s,1);
  }

  // add limiter registration in boot()
  $s=preg_replace('/public function boot\(\)\s*\{/','public function boot(){'."\n        "."app(RateLimiter::class)->for('forms', function(Request \$r){ return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by(\$r->ip()); });\n",$s,1,$c1);
  if(!$c1){ echo "[WARN] Could not inject limiter; add it manually in RouteServiceProvider::boot().\n"; }
  copy($f,"$f.bak_".date('Ymd_His')); file_put_contents($f,$s); echo "[OK] Limiter registered (forms: 20/min/IP)\n";
} else {
  echo "[SKIP] forms limiter already present\n";
}

# attach throttle middleware to routes if not present
$rf='routes/web.php';
$src=file_get_contents($rf);
$replaced=preg_replace('/(Route::post\s*\([^\)]*["\'](?:register|org\/register|contact)["\'][^\;]*)(;)/','${1}->middleware(\'throttle:forms\')$2',$src,-1,$cnt);
if($cnt>0){ copy($rf,"$rf.bak_".date('Ymd_His')); file_put_contents($rf,$replaced); echo "[OK] Attached throttle:forms to $cnt route(s)\n"; }
else { echo "[SKIP] No matching POST routes found or already throttled\n"; }
