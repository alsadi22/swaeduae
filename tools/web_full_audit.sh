#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
# Full website audit for public theme/layout + protected flows
ROOT="https://swaeduae.ae"
ADMIN="https://admin.swaeduae.ae"

section(){ echo -e "\n=== $1 ==="; }
probe(){ # probe BASE PATH EXPECT_CODE
  local base="$1" path="$2" expect="$3"
  local code; code=$(curl -s -o /dev/null -w "%{http_code}" "$base$path" || echo 000)
  printf "%-48s -> %-3s (expect %s)\n" "$base$path" "$code" "$expect"
}

section "Env / Versions (artisan about)"
php artisan about | sed -n '1,80p' || true

section "Nginx / PHP-FPM sanity"
sudo nginx -t 2>&1 | tail -n +1 || true
php -v | head -n 2

section "Rebuild caches"
php artisan route:clear >/dev/null 2>&1
php artisan route:cache  >/dev/null 2>&1 && echo "Routes cached"
php artisan view:clear   >/dev/null 2>&1 && echo "Views cleared"

section "Critical config"
grep -E '^(APP_ENV|APP_DEBUG|APP_URL|SESSION_DOMAIN|SESSION_SECURE_COOKIE)=' .env || true

section "Key files (PWA/SEO)"
for f in public/manifest.json public/service-worker.js public/sitemap.xml public/robots.txt; do
  [ -e "$f" ] && echo "OK   $f" || echo "MISS $f"
done

section "Layouts present (TravelPro public / Argon admin)"
for f in resources/views/public/layout.blade.php resources/views/admin/layouts/*.blade.php resources/views/admin/dashboard.blade.php resources/views/admin/index.blade.php; do
  ls -1 $f 2>/dev/null | sed 's/^/OK   /' || true
done

section "Layout mixing checks (warn if public extends admin or vice-versa)"
# Public views accidentally extending admin?
grep -RIn "@extends(['\"]admin" resources/views 2>/dev/null | grep -v "/admin/" && echo "WARN: public view extends admin layout" || echo "OK   no public->admin extend found"
# Admin views accidentally extending public?
grep -RIn "@extends(['\"]public" resources/views/admin 2>/dev/null && echo "WARN: admin view extends public layout" || echo "OK   no admin->public extend found"

section "Route shadow guard (static dirs that shadow routes)"
if [ -x tools/route_shadow_guard.sh ]; then
  bash tools/route_shadow_guard.sh || true
else
  echo "NOTE: tools/route_shadow_guard.sh not found (skip)"
fi

section "HTTP probes (public pages)"
probe "$ROOT"        "/"                 200
probe "$ROOT"        "/opportunities"    200
probe "$ROOT"        "/partners"         200
probe "$ROOT"        "/qr/verify"        200
# Optional pages (will show 404 if not implemented; that's OK during build)
probe "$ROOT"        "/about"            200
probe "$ROOT"        "/contact"          200
probe "$ROOT"        "/privacy"          200
probe "$ROOT"        "/terms"            200

section "HTTP probes (protected pages as GUEST: expect 302)"
probe "$ROOT"        "/applications"     302
probe "$ROOT"        "/certificates"     302
probe "$ROOT"        "/my/profile"       302
probe "$ADMIN"       "/admin"            302

section "Route names (admin dashboard + logout)"
php artisan route:list | egrep "admin.*Controllers\\\Admin\\\DashboardController@index|logout\.perform|POST\s+logout" || true

section "Admin controllers wired (presence)"
php artisan route:list | egrep "admin\.(users|organizations|opportunities|applicants|certs|attendance|hours|reports|settings)\.|admin\s+.*DashboardController@index" || true

section "Migrations status (top 20)"
php artisan migrate:status | sed -n '1,120p' || true

section "Queue worker status"
systemctl --no-pager status swaed-queue-worker 2>/dev/null | sed -n '1,20p' || echo "NOTE: queue worker service not found"

section "DB quick counts (users / orgs / opps / certs)"
php -r 'require "vendor/autoload.php"; $a=require "bootstrap/app.php"; $a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "users=".\DB::table("users")->count()."\n";
echo "org_profiles=".\DB::table("org_profiles")->count()."\n";
echo "opportunities=".\DB::table("opportunities")->count()."\n";
echo "certificates=".\DB::table("certificates")->count()."\n";' 2>/dev/null || echo "NOTE: DB quick counts skipped"

section "Recent errors (today)"
tail -n 80 storage/logs/laravel-$(date +%F).log 2>/dev/null || echo "No log for today"

section "DONE"
