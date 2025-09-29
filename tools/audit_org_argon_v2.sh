#!/usr/bin/env bash
set -euo pipefail
main="resources/views/org/dashboard.blade.php"

echo "== View that serves org.dashboard =="
php -r 'require "vendor/autoload.php"; $a=require "bootstrap/app.php";
$a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$v=view("org.dashboard"); echo (method_exists($v,"getPath")?$v->getPath():"(no getPath)"),PHP_EOL;'

echo
echo "== Files included by $main (1 level) =="
files=("$main")
mapfile -t incs < <(grep -oE "@include\(['\"][^)]+['\"]\)" "$main" | sed -E "s/.*\(['\"]([^'\"]+)['\"].*/\1/")
for inc in "${incs[@]}"; do
  f="resources/views/${inc//./\/}.blade.php"; [[ -f "$f" ]] && files+=("$f")
done
[[ -f resources/views/org/partials/dashboard_v1.blade.php ]] && files+=("resources/views/org/partials/dashboard_v1.blade.php")
printf "%s\n" "${files[@]}" | nl -ba

echo
echo "== Variables referenced in those blades =="
grep -hRoE '\$[A-Za-z_][A-Za-z0-9_]*' "${files[@]}" \
 | sed -E 's/[^$A-Za-z0-9_].*//' \
 | grep -Ev '^\$(loop|errors|message|slot|__data|__path|attributes)$' \
 | sort -u | nl -ba

echo
echo "== Controller method & return(view(...)) lines =="
php -r '
require "vendor/autoload.php"; $a=require "bootstrap/app.php";
$a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$routes=app("router")->getRoutes(); $r=$routes->getByName("org.dashboard");
[$cls,$m]=explode("@",$r->getActionName(),2); $rm=new ReflectionMethod($cls,$m);
$path=$rm->getFileName(); $s=$rm->getStartLine()-5; $e=$rm->getEndLine()+5; if($s<1)$s=1;
echo $path,PHP_EOL;
$src=file($path); for($i=$s;$i<=$e;$i++){ printf("%6d  %s",$i,$src[$i-1]??""); }'

echo
echo "== Recent dashboard-related errors =="
( tail -n 300 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 300 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i "org|dashboard|ERROR|exception|Undefined|variable|view" | tail -n 40 || true
