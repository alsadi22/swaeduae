PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
fail(){ echo "[FAIL] $*"; exit 1; }
pass(){ echo "[OK]   $*"; }

echo "== ROUTE NAMES =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Route;
$need=["admin.login","admin.dashboard","admin.users.index","admin.opportunities.index","login","register","org.login","org.register","org.dashboard"];
foreach ($need as $n) { echo $n." ".(Route::has($n)?"YES":"NO").PHP_EOL; }' |
awk '{if($2!="YES"){print "[FAIL] missing route: "$1; exit 1}} END{if(NR>0) print "[OK]   all required routes present"}' || exit 1

echo "== ADMIN MIDDLEWARE =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Route;
foreach (Route::getRoutes() as $r){
  $n=$r->getName();
  if($n && str_starts_with($n,"admin.")){
    echo $n."|".implode(",", $r->gatherMiddleware()).PHP_EOL;
  }
}' > storage/_admin_mw.txt
grep -q '^admin\.dashboard\|.*auth.*verified.*can:admin' storage/_admin_mw.txt || fail "admin.dashboard missing auth+verified+can:admin"
grep -q '^admin\.users\.index\|.*auth.*verified.*can:admin' storage/_admin_mw.txt || fail "admin.users.index missing auth+verified+can:admin"
grep -q '^admin\.opportunities\.index\|.*auth.*verified.*can:admin' storage/_admin_mw.txt || fail "admin.opportunities.index missing auth+verified+can:admin"
pass "admin routes protected by auth+verified+can:admin"

echo "== HTTP BEHAVIOR (via CF->localhost, -k) =="
D=swaeduae.ae
check(){
  path="$1"; code_expected="$2"; location_expected="${3:-}"
  H=$(curl -sI -k --resolve $D:443:127.0.0.1 https://$D$path)
  code=$(echo "$H" | awk 'toupper($1) ~ /^HTTP/ {print $2; exit}')
  loc=$(echo "$H" | awk 'tolower($1)=="location:" {print $2; exit}')
  if [ "$code" != "$code_expected" ]; then fail "$path expected $code_expected got $code"; fi
  if [ -n "$location_expected" ] && [ "$loc" != "$location_expected" ]; then fail "$path expected Location $location_expected got ${loc:-<none>}"; fi
  pass "$path -> $code ${loc:+($loc)}"
}
check /admin 302 https://$D/admin/login
check /admin/login 200
check /admin/dashboard 302 https://$D/admin/login
check /org 302 https://$D/org/login
check /org/login 200
check /login 200
echo "[OK]   HTTP behavior matches spec"
