<?php
$it = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator('routes', FilesystemIterator::SKIP_DOTS)
);
$files=[]; foreach($it as $p){ if($p->getExtension()==='php') $files[]=$p->getPathname(); }
$tot=['login'=>0,'register'=>0,'contact'=>0];

foreach($files as $f){
  $s=file_get_contents($f); $orig=$s;

  // Add middleware only to POST routes we care about
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/login['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot','throttle:login'])$2",
    $s, -1, $c1
  );
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/register['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot'])$2",
    $s, -1, $c2
  );
  $s=preg_replace(
    "/Route::post\\((\\s*['\"]\\/contact['\"][^;]*?)(\\)\\s*;)/s",
    "Route::post($1->middleware(['honeypot'])$2",
    $s, -1, $c3
  );

  if ($s !== $orig) {
    copy($f,"$f.bak_".date('Ymd_His'));
    file_put_contents($f,$s);
    echo "[OK] $f (login:$c1 register:$c2 contact:$c3)\n";
  }
  $tot['login']+=$c1; $tot['register']+=$c2; $tot['contact']+=$c3;
}
printf("[SUMMARY] login:%d register:%d contact:%d\n", $tot['login'], $tot['register'], $tot['contact']);
