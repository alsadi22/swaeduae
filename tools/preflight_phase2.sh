#!/usr/bin/env bash
set -u
ROOT="/home3/vminingc/swaeduae.ae"
APP="/home3/vminingc/swaeduae.ae/laravel-app"
PHP="/usr/bin/php"

echo "=== Phase 2 preflight (read-only) $(date -Is) ==="

# 0) Paths, PHP, Laravel basics
echo "--- Basics ---"
ls -ld "$ROOT" "$APP"
$PHP -v | head -n1
cd "$APP" && pwd
$PHP artisan --version
$PHP artisan about | egrep 'Laravel|PHP|Environment|Timezone|Locale' || true
grep -n '^APP_URL=' .env || true
echo

# 1) Routing baseline in place (volunteer/org/admin)
echo "--- Routes: public/org/admin ---"
$PHP artisan route:list | egrep -i '^\s*(GET|POST)\s+/(signin|login|logout|register)|/org/(login|register)|^.*\sadmin' || true
grep -RIn "Route::get\('/signin" routes || true
grep -RIn "/org/login" routes || true
echo

# 2) Admin separation sanity
echo "--- Admin sanity ---"
grep -RIn "admin\." resources/views/admin routes || true
$PHP artisan route:list | egrep -i 'admin/(login|logout|dashboard|users)' || true
echo

# 3) Docroot wiring (webroot -> Laravel)
echo "--- Webroot wiring ---"
ls -l "$ROOT" | egrep 'index\.php|\.htaccess|public' || true
head -n 40 "$ROOT/index.php" 2>/dev/null || echo "[info] no index.php at webroot"
test -f "$APP/public/index.php" && sed -n '1,12p' "$APP/public/index.php"
echo

# 4) Locale & timezone defaults
echo "--- config/app.php locale/timezone ---"
grep -n "'locale'"   config/app.php || true
grep -n "'fallback_locale'" config/app.php || true
grep -n "'timezone'" config/app.php || true
echo

# 5) Kernel has middleware aliases
echo "--- Kernel middlewareAliases ---"
grep -n "protected \$middlewareAliases" app/Http/Kernel.php || sed -n '1,120p' app/Http/Kernel.php
echo

# 6) Public layout(s) with <head> (exclude admin)
echo "--- Public layouts with <head> ---"
grep -RIl --binary-files=without-match '<head' resources/views | grep -v '/admin/' | head -n 3 || true
echo

# 7) Scheduler presence
echo "--- Scheduler ---"
$PHP artisan schedule:list || true

echo "[Preflight done] $(date -Is)"
