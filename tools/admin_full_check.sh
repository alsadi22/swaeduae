#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date '+%F_%H%M%S')"
OUT="public/health/admin-check-$STAMP.txt"
exec > >(tee -a "$OUT") 2>&1

echo "=== ADMIN FULL CHECK $STAMP ==="
echo "PWD: $(pwd)"
echo "USER: $(whoami)"
echo

echo "== PHP & APP ENV =="
php -v || true
composer --version || true
APP_ENV=$(grep -E '^APP_ENV=' .env | cut -d= -f2- || true)
APP_URL=$(grep -E '^APP_URL=' .env | cut -d= -f2- || true)
echo "APP_ENV=$APP_ENV"
echo "APP_URL=$APP_URL"
echo

echo "== LINT CRITICAL ROUTE FILES =="
php -l routes/web.php || true
[ -f routes/admin.php ] && php -l routes/admin.php || echo "[WARN] routes/admin.php missing"
[ -f routes/partials/disable_internal.php ] && echo "[OK] routes/partials/disable_internal.php present" || echo "[WARN] disable_internal.php missing"
echo

echo "== ADMIN CONTROLLERS EXIST & LINT =="
for c in app/Http/Controllers/Admin/DashboardController.php \
         app/Http/Controllers/Admin/UserController.php \
         app/Http/Controllers/Admin/OpportunityController.php; do
  if [ -f "$c" ]; then php -l "$c" && echo "[OK] $c"; else echo "[FAIL] missing: $c"; fi
done
echo

echo "== ADMIN VIEWS EXIST & COMPILE =="
for v in resources/views/admin/layout.blade.php \
         resources/views/admin/dashboard.blade.php \
         resources/views/admin/users/index.blade.php \
         resources/views/admin/opportunities/index.blade.php; do
  [ -f "$v" ] && echo "[OK] view exists: $v" || echo "[FAIL] missing view: $v"
done
php artisan view:clear && php artisan view:cache || echo "[FAIL] blade compile error"
echo

echo "== ROUTES OVERVIEW (saving to storage/admin-routes-$STAMP.txt) =="
php artisan route:list > "storage/admin-routes-$STAMP.txt" || true
grep -E '(^|\s)/?admin(/|$)' "storage/admin-routes-$STAMP.txt" || echo "[WARN] could not find visible 'admin' rows (table output may vary)"
echo

echo "== VERIFY ADMIN ROUTE MIDDLEWARE (storage/admin-mw-$STAMP.txt) =="
php artisan tinker --execute='
use Illuminate\Support\Facades\Route;
foreach (Route::getRoutes() as $r) {
  $n = $r->getName();
  if ($n && str_starts_with($n,"admin.")) {
    echo $n."|".implode(",", $r->gatherMiddleware()).PHP_EOL;
  }
}' > "storage/admin-mw-$STAMP.txt" || true
cat "storage/admin-mw-$STAMP.txt" || true
if [ -s "storage/admin-mw-$STAMP.txt" ]; then
  grep -q 'auth' "storage/admin-mw-$STAMP.txt" || echo "[FAIL] some admin routes missing 'auth'"
  grep -q 'verified' "storage/admin-mw-$STAMP.txt" || echo "[FAIL] some admin routes missing 'verified'"
  grep -q 'can:admin' "storage/admin-mw-$STAMP.txt" || echo "[FAIL] some admin routes missing 'can:admin'"
else
  echo "[WARN] no named admin routes recorded"
fi
echo

echo "== GATE & EMAIL VERIFICATION CHECKS =="
grep -RFn -- 'Gate::define('\''admin'\''' app/Providers && echo "[OK] admin gate present" || echo "[FAIL] admin gate not defined in AuthServiceProvider"
grep -n "MustVerifyEmail" app/Models/User.php && echo "[OK] User implements MustVerifyEmail" || echo "[WARN] User model not implementing MustVerifyEmail"
echo

echo "== SCHEMA & ADMIN USER ROLE =="
php artisan tinker --execute="use Illuminate\\Support\\Facades\\Schema; echo 'users.role column: '.(Schema::hasColumn('users','role')?'YES':'NO').PHP_EOL;"
php artisan tinker --execute="use App\\Models\\User; \$u=User::where('email','admin@swaeduae.ae')->first(); echo 'admin@swaeduae.ae role: '.(\$u?(\$u->role??'null'):'MISSING').PHP_EOL;"
echo

echo "== NAMED ROUTES EXIST? =="
php artisan tinker --execute="use Illuminate\\Support\\Facades\\Route; foreach (['admin.dashboard','admin.users.index','admin.opportunities.index'] as \$r) { echo \$r.': '.(Route::has(\$r)?'YES':'NO').PHP_EOL; }"
echo

echo "== HTTP ENDPOINT SMOKE (CF -> 127.0.0.1, -k) =="
DOMAIN="$(echo "$APP_URL" | sed -E 's#https?://##; s#/+$##')"
for path in /admin /admin/users /admin/opportunities; do
  printf "%-26s -> " "$path"
  curl -sS -k -o /dev/null -w "%{http_code}" --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN$path" || echo "curl-error"
  echo
done
echo

echo "== RECENT LOG LINES (admin-related) =="
if [ -f storage/logs/laravel.log ]; then
  tail -n 2000 storage/logs/laravel.log | grep -i admin || echo "no 'admin' mentions in last 2000 lines"
else
  echo "no laravel.log"
fi
echo

echo "== SUMMARY =="
echo "Routes file dump: storage/admin-routes-$STAMP.txt"
echo "Admin MW dump:    storage/admin-mw-$STAMP.txt"
echo "Report saved:     $OUT"
echo "=== END ==="
