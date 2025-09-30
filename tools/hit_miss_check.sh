#!/usr/bin/env bash
set -euo pipefail
BASE="${BASE:-https://swaeduae.ae}"
HLOG_DIR="public/health"
mkdir -p "$HLOG_DIR"

green=$'\e[32m'; red=$'\e[31m'; yellow=$'\e[33m'; reset=$'\e[0m'
HIT(){ echo "${green}[HIT]${reset} $*"; }
MISS(){ echo "${red}[MISS]${reset} $*"; FAILED=1; }
WARN(){ echo "${yellow}[WARN]${reset} $*"; }

FAILED=0
SUMMARY="$(date +'%Y-%m-%d_%H%M%S')"
OUT="$HLOG_DIR/hitmiss-$SUMMARY.txt"
exec > >(tee -a "$OUT") 2>&1

echo "=== HIT/MISS CHECK ($(date -u +"%F %T") UTC) ==="
echo "BASE=$BASE"

echo
echo "-- Artisan caches --"
php artisan optimize:clear >/dev/null && HIT "optimize:clear ran" || MISS "optimize:clear failed"
php artisan route:cache >/dev/null && HIT "route:cache compiled" || MISS "route:cache failed"
php artisan view:cache >/dev/null && HIT "view:cache compiled"

echo
echo "-- Routes + middleware --"
rl(){ php artisan route:list

if rl | grep -E "account/(applications|certificates)" >/dev/null; then
  if rl | grep -E "account/(applications|certificates)" | grep -Eiq "auth"; then
    HIT "account/* protected by auth (route:list)"
  else
    MISS "account/* missing auth (route:list)"
  fi
else
  # Fallback via HTTP
  code_app=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE/account/applications")
  loc_app=$(curl -sD - -o /dev/null "$BASE/account/applications" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}')
  code_cert=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE/account/certificates")
  loc_cert=$(curl -sD - -o /dev/null "$BASE/account/certificates" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}')
  if [[ "$code_app" == "302" && "$loc_app" =~ /login ]] && [[ "$code_cert" == "302" && "$loc_cert" =~ /login ]]; then
    HIT "account/* protected (detected via curl fallback)"
  else
    MISS "account/* routes not detected"
  fi
fi

if rl | grep -E "opportunities/.*/apply" >/dev/null; then
  rl | grep -E "opportunities/.*/apply" | grep -Eiq "auth" \
    && HIT "opportunities.apply protected" || MISS "opportunities.apply not protected"
else
  WARN "opportunities.apply route not found"
fi

# QR verify presence: route OR HTTP
if php artisan route:list| grep -E "^qr/verify" >/dev/null; then
  HIT "QR verify endpoint present (route:list)"
else
  code_verify=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE/qr/verify")
  [[ "$code_verify" == "200" ]] && HIT "QR verify endpoint present (curl)" || MISS "QR verify endpoint missing"
fi

# ICS endpoint presence
if php artisan route:list| grep -E "^ics/" >/dev/null; then
  HIT "ICS endpoint present"
else
  WARN "ICS endpoint not found"
fi

echo
echo "-- Public endpoints (logged OUT) --"
check_code(){ local path="$1" code="$2"; local got; got=$(curl -s -o /dev/null -w "%{http_code}" -I "$BASE$path"); [[ "$got" == "$code" ]] && HIT "$path -> $got" || MISS "$path -> $got (expected $code)"; }
check_loc(){ local path="$1" expect="$2"; local loc; loc=$(curl -sD - -o /dev/null "$BASE$path" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}'); [[ "$loc" =~ $expect ]] && HIT "$path redirects to $loc" || MISS "$path wrong redirect ($loc), expect /$expect"; }

for u in / /opportunities /about /contact /privacy /terms /qr/verify; do check_code "$u" 200; done
check_code /admin/login 200
check_code /account/applications 302 && check_loc /account/applications login
check_code /account/certificates 302 && check_loc /account/certificates login

echo
echo "-- ENV hardening --"
grep -qE '^APP_DEBUG=false' .env && HIT "APP_DEBUG=false" || MISS "APP_DEBUG not false"
grep -qE '^APP_ENV=production' .env && HIT "APP_ENV=production" || WARN "APP_ENV not production"
grep -qE '^QUEUE_CONNECTION=sync' .env && WARN "Queue=sync (ok for MVP, upgrade later)" || HIT "Queue not sync"

echo
echo "-- Provider & models --"
grep -q "ViewServiceProvider::class" config/app.php && HIT "ViewServiceProvider registered" || MISS "ViewServiceProvider not registered"
test -f app/Providers/ViewServiceProvider.php && HIT "ViewServiceProvider file exists" || MISS "ViewServiceProvider file missing"
test -f app/Models/Application.php && HIT "Application model exists" || WARN "Application model missing"
test -f app/Models/Certificate.php && HIT "Certificate model exists" || WARN "Certificate model missing"

echo
echo "-- Storage / PWA / Sitemap --"
test -e public/storage && HIT "public/storage present" || WARN "public/storage missing (php artisan storage:link)"
test -f public/manifest.json && HIT "manifest.json present" || WARN "manifest.json missing"
test -f public/service-worker.js && HIT "service-worker.js present" || WARN "service-worker.js missing"
test -f public/img/icon-192.png && test -f public/img/icon-512.png && HIT "PWA icons present" || WARN "PWA icons missing"
test -L public/sitemap.xml && HIT "sitemap.xml symlink present" || WARN "sitemap.xml symlink missing"
test -d public/sitemaps && ls -1 public/sitemaps/* >/dev/null 2>&1 && HIT "sitemaps directory has files" || WARN "sitemaps directory empty"

echo
echo "-- Logs scan --"
latest=$(ls -1t storage/logs/laravel-*.log 2>/dev/null | head -n1 || true)
if [[ -n "${latest:-}" ]]; then
  echo "Latest log: $latest"
  if grep -Eiq "(ERROR|CRITICAL|ParseError|TypeError|Undefined|SQLSTATE)" "$latest"; then
    WARN "Errors present in latest log (last 20)"
    tail -n 20 "$latest"
  else
    HIT "No obvious errors in latest log"
  fi
else
  WARN "No laravel log found"
fi

echo
echo "-- Health page --"
echo "Saved report: $OUT"
[[ $FAILED -eq 0 ]] && echo "${green}SUMMARY: ALL CORE CHECKS PASSED${reset}" || echo "${red}SUMMARY: SOME CHECKS FAILED${reset}"
exit $FAILED
