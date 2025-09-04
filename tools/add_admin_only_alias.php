<?php
$f='app/Providers/MiddlewareAliasesServiceProvider.php';
if(!file_exists($f)){fwrite(STDERR,"[ERR] $f missing\n"); exit(1);}
$s=file_get_contents($f);
if(strpos($s,"aliasMiddleware('admin.only'")===false){
  $s=preg_replace(
    "/aliasMiddleware\\('microcache'[^;]*;\\s*/",
    "$0        \$router->aliasMiddleware('admin.only', \\App\\Http\\Middleware\\AdminOnly::class);\n",
    $s,1,$c
  );
  if(!$c){ echo "[WARN] microcache alias not found; appending\n"; $s=preg_replace('/public function boot\\([^)]+\\)\\s*\\{/','\\0'."\n        \$router->aliasMiddleware('admin.only', \\App\\Http\\Middleware\\AdminOnly::class);\n",$s,1,$c2); }
  copy($f,"$f.bak_".date('Ymd_His')); file_put_contents($f,$s);
  echo "[OK] admin.only alias added\n";
} else { echo "[SKIP] admin.only already present\n"; }
