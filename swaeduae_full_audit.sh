#!/usr/bin/env bash
# ===========================================
# SWAEDUAE FULL READ-ONLY AUDIT (NO WRITES)
# ===========================================
set -u

cd /var/www/swaeduae || exit 1

echo; echo "=== VERSIONS & BASIC CONTEXT ==="
php -v | head -n1 || true
php artisan --version || true
git rev-parse --short HEAD 2>/dev/null || echo "(no git)"
git status -sb 2>/dev/null || true

echo; echo "=== CRITICAL ROUTES SNAPSHOT (login/signin/org/opps/avatar/uaepASS/qr) ==="
php artisan route:list | egrep -i \
"GET *\\| *signin|/sign-in|^ *GET *\\| *login|^ *POST *\\| *login|^ *POST *\\| *logout|org/(login|register)|opportunities( |/)|profile/avatar|uae ?pass|qr|check-?in|check-?out" \
|| true

# -------------------------
# ROUTES ON DISK & PROVIDERS
# -------------------------
echo; echo "=== routes/web.php (1..220) ==="
nl -ba routes/web.php | sed -n '1,220p'

[ -f routes/_opportunities.php ] && { echo; echo "=== routes/_opportunities.php ==="; nl -ba routes/_opportunities.php; }

echo; echo "=== Route files present ==="
find routes -maxdepth 1 -type f -name "*.php" -print | sort

echo; echo "=== Any Route:: definitions under routes/ ==="
grep -RIn --color -E "Route::(get|post|put|patch|delete|match)\(" routes | sort || true

echo; echo "=== Registered Service Providers (bootstrap/providers.php) ==="
[ -f bootstrap/providers.php ] && nl -ba bootstrap/providers.php | sed -n '1,220p' || echo "(no bootstrap/providers.php)"

echo; echo "=== Providers that may bind routes/middleware ==="
for f in \
  app/Providers/LoadAllRoutesServiceProvider.php \
  app/Providers/ViewComposerServiceProvider.php \
  app/Providers/OrgDashboardServiceProvider.php \
  app/Providers/DashboardCacheServiceProvider.php \
  app/Providers/UaePassSocialiteServiceProvider.php \
  app/Providers/SpatieAliasesServiceProvider.php \
  app/Providers/MiddlewareAliasesServiceProvider.php \
  app/Providers/OrgShortlistRoutesServiceProvider.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 200) ==="; nl -ba "$f" | sed -n '1,200p'; }
done

# -------------------------
# AUTH / UAEPASS WIRING
# -------------------------
echo; echo "=== .env UAEPASS keys present? (values redacted by you) ==="
egrep -n "^(UAEPASS_CLIENT_ID|UAEPASS_CLIENT_SECRET|UAEPASS_REDIRECT_URI|UAEPASS_AUTH_URL|UAEPASS_TOKEN_URL|UAEPASS_USERINFO_URL)=" .env || true

echo; echo "=== config/services.php (look for 'uaepass') ==="
[ -f config/services.php ] && nl -ba config/services.php | sed -n '1,220p' || echo "(no config/services.php)"

echo; echo "=== UAEPASS routes available ==="
php artisan route:list | egrep -i "uae ?pass|uaepass" || true

for f in \
  app/Http/Controllers/Auth/UAEPassController.php \
  app/Providers/UaePassSocialiteServiceProvider.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 220) ==="; nl -ba "$f" | sed -n '1,220p'; }
done

echo; echo "=== Auth blades actually used ==="
for f in \
  resources/views/auth/login.blade.php \
  resources/views/auth/volunteer_login.blade.php \
  resources/views/auth/organization_login.blade.php \
  resources/views/auth/login-fallback.blade.php \
  resources/views/auth/signin-options.blade.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 140) ==="; nl -ba "$f" | sed -n '1,140p'; }
done

# -------------------------
# PUBLIC OPPORTUNITIES (INDEX/SHOW VIEWS)
# -------------------------
echo; echo "=== Public opps blades found ==="
for f in \
  resources/views/opportunities/public/index.blade.php \
  resources/views/opportunities/public/show.blade.php \
  resources/views/opportunities/index.blade.php \
  resources/views/opportunities/public_show.blade.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 160) ==="; nl -ba "$f" | sed -n '1,160p'; }
done

# -------------------------
# QR / MINUTES / GEO — BACKEND
# -------------------------
echo; echo "=== Controllers touching QR / checkin-out / minutes / geo ==="
grep -RIn --color -E "(^|[^a-z])(QR|Qr|qr)([^a-z]|$)|check-?in|check-?out|diffInMinutes|\\bminutes\\b|geoloc|latitude|longitude|\\blat\\b|\\blng\\b|haversine" app | sort || true

for f in \
  app/Http/Controllers/EventQrController.php \
  app/Http/Controllers/QRScannerController.php \
  app/Http/Controllers/QrScanController.php \
  app/Http/Controllers/AttendanceController.php \
  app/Http/Controllers/Admin/AttendanceAdminController.php \
  app/Http/Controllers/Org/AttendanceController.php \
  app/Http/Controllers/Admin/OpportunityQRController.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 220) ==="; nl -ba "$f" | sed -n '1,220p'; }
done

# -------------------------
# QR / GEO — FRONTEND JS & BLADES
# -------------------------
echo; echo "=== JS/Blade references to scanners & geolocation ==="
grep -RIn --color -E "Html5Qrcode|ZXing|Quagga|navigator\\.geolocation|getCurrentPosition|watchPosition|\\bqr\\b" resources/js public/js resources/views | sort || true

for f in \
  resources/views/qr.blade.php \
  resources/views/opportunities/qr.blade.php \
  resources/views/public/qr.blade.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f (first 160) ==="; nl -ba "$f" | sed -n '1,160p'; }
done

# -------------------------
# VOLUNTEER DASHBOARD / AVATAR
# -------------------------
echo; echo "=== Volunteer profile dashboard (first 220) ==="
[ -f resources/views/volunteer/profile.blade.php ] && nl -ba resources/views/volunteer/profile.blade.php | sed -n '1,220p' || echo "(no volunteer/profile.blade.php)"

for f in \
  resources/views/volunteer/partials/avatar_form.blade.php \
  resources/views/components/avatar-controls.blade.php \
; do
  [ -f "$f" ] && { echo; echo "=== $f ==="; nl -ba "$f"; }
done

echo; echo "=== Storage & avatar placeholder asset ==="
[ -L public/storage ] && echo "public/storage symlink: OK" || echo "public/storage symlink: MISSING"
[ -d storage/app/public/avatars ] && echo "avatars dir exists" || echo "avatars dir MISSING"
[ -f public/images/avatar-placeholder.svg ] && echo "avatar placeholder present" || echo "avatar placeholder MISSING"

# -------------------------
# DATABASE REALITY CHECKS (safe reads)
# -------------------------
echo; echo "=== Tables exist? (opportunities + hours/applications/qr_scans/geo_logs) ==="
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema;
foreach (["opportunities","volunteer_hours","opportunity_applications","qr_scans","geo_logs"] as $t) {
  echo $t.": ".(Schema::hasTable($t)?"YES":"NO").PHP_EOL;
}
' || true

echo; echo "=== Row counts (non-fatal if empty) ==="
php artisan tinker --execute='
use Illuminate\Support\Facades\DB;
foreach (["volunteer_hours","opportunity_applications","qr_scans","geo_logs"] as $t) {
  echo "$t: ";
  try { echo DB::table($t)->count().PHP_EOL; } catch (\Throwable $e) { echo "ERR".PHP_EOL; }
}
' || true

echo; echo "=== SHOW COLUMNS (hours/apps/qr_scans/geo_logs) ==="
php artisan tinker --execute='
use Illuminate\Support\Facades\DB;
foreach (["volunteer_hours","opportunity_applications","qr_scans","geo_logs"] as $t) {
  echo "\n-- $t --\n";
  try { foreach (DB::select("SHOW COLUMNS FROM `$t`") as $c) { echo $c->Field." ".$c->Type.PHP_EOL; } }
  catch (\Throwable $e) { echo "ERR".PHP_EOL; }
}
' || true

echo; echo "=== users table avatar columns present? ==="
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema;
foreach ([["users","avatar"],["users","avatar_path"]] as [$tbl,$col]) {
  echo "$tbl.$col: ".(Schema::hasColumn($tbl,$col)?"YES":"NO").PHP_EOL;
}
' || true

echo; echo "=== opportunities columns present (to match views) ==="
php artisan tinker --execute='
use Illuminate\Support\Facades\Schema;
if (Schema::hasTable("opportunities")) {
  echo json_encode(Schema::getColumnListing("opportunities")).PHP_EOL;
} else { echo "opportunities: NO TABLE".PHP_EOL; }
' || true

# -------------------------
# MIDDLEWARE / PERMISSIONS (admin/org)
# -------------------------
echo; echo "=== MiddlewareAliasesServiceProvider (ensure can/isAdmin) ==="
[ -f app/Providers/MiddlewareAliasesServiceProvider.php ] && nl -ba app/Providers/MiddlewareAliasesServiceProvider.php | sed -n '1,220p' || echo "(no MiddlewareAliasesServiceProvider.php)"

echo; echo "=== config/permission.php (if Spatie present) ==="
[ -f config/permission.php ] && nl -ba config/permission.php | sed -n '1,140p' || echo "(no config/permission.php)"

# -------------------------
# CURL SMOKE (adjust APP_HOST if needed)
# -------------------------
APP_HOST="${APP_HOST:-https://swaeduae.ae}"
echo; echo "=== CURL smoke tests (HEAD) against ${APP_HOST} ==="
for u in \
  /signin \
  "/signin?type=organization" \
  "/signin?type=volunteer" \
  /opportunities \
  /login \
  /org/login \
; do
  echo -e "\n-- $u"
  curl -I -sS "${APP_HOST}${u}" | sed -n '1,5p'
done

echo; echo "=== DONE ==="
