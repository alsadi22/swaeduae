<?php
$f='routes/web.php';
$s=file_get_contents($f);
if($s===false){fwrite(STDERR,"[ERR] cannot read $f\n"); exit(1);}

$rx='/Route::post\s*\((?:(?>[^()]+)|(?R))*\)\s*[^;]*->name\s*\(\s*[\'"]org\.register\.submit[\'"]\s*\)\s*;/s';
if(!preg_match($rx,$s,$m,PREG_OFFSET_CAPTURE)){
  echo "[WARN] could not locate Route::post(...)->name('org.register.submit');\n"; exit(0);
}

$stmt = $m[0][0];
if (stripos($stmt,'Honeypot')!==false || stripos($stmt,"->middleware('honeypot'")!==false) {
  echo "[SKIP] honeypot already present\n"; exit(0);
}

$pos = stripos($stmt,'->name(');
$inj = "->middleware(\\App\\Http\\Middleware\\Honeypot::class)";
$patched = substr($stmt,0,$pos).$inj.substr($stmt,$pos);

copy($f,"$f.bak_".date('Ymd_His'));
$s = substr($s,0,$m[0][1]) . $patched . substr($s,$m[0][1]+strlen($stmt));
file_put_contents($f,$s);
echo "[OK] injected honeypot on POST org/register\n";
