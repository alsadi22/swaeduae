<?php
$targets = ['register','org/register','contact'];
$files = glob(__DIR__.'/../routes/*.php') ?: [];
$did=0;
foreach($files as $f){
  $s=file_get_contents($f); if($s===false) continue;
  $orig=$s;
  foreach($targets as $u){
    // match Route::post('uri' ... ); and inject ->middleware('throttle:forms') before ->name(..) or ending ;
    $s=preg_replace(
      '#(Route::post\s*\(\s*[\'"]'.$u.'[\'"][^;]*?)(->name\s*\([^;]*\))?;#s',
      function($m){
        $stmt=$m[1];
        if(stripos($stmt,'throttle:forms')!==false) return $m[0]; // already present
        $inject="->middleware('throttle:forms')";
        if(!empty($m[2])){ // has ->name(...)
          return $stmt.$inject.$m[2].';';
        }
        return rtrim($stmt).$inject.';';
      },
      $s,-1,$c
    );
    if($c) $did+=$c;
  }
  if($s!==$orig){
    copy($f,$f.'.bak_'.date('Ymd_His'));
    file_put_contents($f,$s);
    echo "[OK] Patched $f\n";
  }
}
echo $did ? "[DONE] throttle:forms added to $did occurrence(s)\n" : "[SKIP] nothing to patch\n";
