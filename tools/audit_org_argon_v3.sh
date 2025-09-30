#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
main="resources/views/org/dashboard.blade.php"

echo "== view('org.dashboard') path =="
php -r 'require "vendor/autoload.php"; $a=require "bootstrap/app.php";
$a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$v=view("org.dashboard"); echo (method_exists($v,"getPath")?$v->getPath():"(no getPath)"),PHP_EOL;'

echo
echo "== 1-level includes used by $main =="
files=("$main")
tmp=_incs.$$.txt
grep -oE "@include\(['\"][^)]+['\"]\)" "$main" | sed -E "s/.*\(['\"]([^'\"]+)['\"].*/\1/" > "$tmp" || true
while IFS= read -r inc; do
  f="resources/views/${inc//./\/}.blade.php"; [[ -f "$f" ]] && files+=("$f")
done < "$tmp"
rm -f "$tmp"
[[ -f resources/views/org/partials/dashboard_v1.blade.php ]] && files+=("resources/views/org/partials/dashboard_v1.blade.php")
nl -ba <(printf "%s\n" "${files[@]}")

echo
echo "== Variables referenced in those blades =="
grep -hRoE '\$[A-Za-z_][A-Za-z0-9_]*' "${files[@]}" \
 | sed -E 's/[^$A-Za-z0-9_].*//' \
 | grep -Ev '^\$(loop|errors|message|slot|__data|__path|attributes)$' \
 | sort -u | nl -ba

echo
echo "== Controller method (org.dashboard) =="
php -r '
require "vendor/autoload.php"; $a=require "bootstrap/app.php";
$a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$r=app("router")->getRoutes()->getByName("org.dashboard");
[$cls,$m]=explode("@",$r->getActionName(),2); $rm=new ReflectionMethod($cls,$m);
$path=$rm->getFileName(); $s=$rm->getStartLine()-5; $e=$rm->getEndLine()+5; if($s<1)$s=1;
$src=file($path); echo $path,PHP_EOL; for($i=$s;$i<=$e;$i++){ printf("%6d  %s",$i,$src[$i-1]??""); }'

echo
echo "== Recent dashboard-related errors =="
( tail -n 300 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 300 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i "org|dashboard|ERROR|exception|Undefined|variable|view" | tail -n 40 || true
