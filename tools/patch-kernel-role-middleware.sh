#!/usr/bin/env bash
set -euo pipefail
K="app/Http/Kernel.php"
[[ -f "$K" ]] || { echo "❌ $K not found"; exit 1; }
cp "$K" "${K}.bak.$(date +%s)"
php -r '
$f="app/Http/Kernel.php";
$c=file_get_contents($f);
if($c===false){fwrite(STDERR,"Cannot read $f\n"); exit(1);}
if(strpos($c,"Spatie\\\Permission\\\Middlewares\\\RoleMiddleware")!==false){
  echo "✅ Role middleware already present\n"; exit(0);
}
$needle="protected \$routeMiddleware = [";
$pos=strpos($c,$needle);
if($pos===false){fwrite(STDERR,"❌ Could not find \$routeMiddleware array in $f\n"); exit(1);}
$insert = "        \\'role\\' => \\\\Spatie\\\\Permission\\\\Middlewares\\\\RoleMiddleware::class,\n"
        . "        \\'permission\\' => \\\\Spatie\\\\Permission\\\\Middlewares\\\\PermissionMiddleware::class,\n"
        . "        \\'role_or_permission\\' => \\\\Spatie\\\\Permission\\\\Middlewares\\\\RoleOrPermissionMiddleware::class,\n";
$c = preg_replace("/protected \\\$routeMiddleware = \\[/","protected \$routeMiddleware = [\n".$insert,$c,1);
file_put_contents($f,$c);
echo "✅ Injected Spatie role middleware aliases into $f\n";
'
