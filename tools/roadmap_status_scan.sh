#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -Eeuo pipefail
APP="/var/www/swaeduae/current"
OUT="$APP/tools/reports"; TS="$(date +%Y%m%d_%H%M%S)"; mkdir -p "$OUT"
R="$OUT/ROADMAP_STATUS_${TS}.txt"
PASS=0; WARN=0; FAIL=0
ok(){ echo -e "\033[1;32m[PASS]\033[0m $*"; ((PASS++)); }
warn(){ echo -e "\033[1;33m[WARN]\033[0m $*"; ((WARN++)); }
fail(){ echo -e "\033[1;31m[FAIL]\033[0m $*"; ((FAIL++)); }

exec > >(tee -a "$R") 2>&1
echo "== SwaedUAE Roadmap Status Scan ($TS) =="

cd "$APP"

# ---------- Helpers ----------
is2xx(){ curl -s -o /dev/null -w '%{http_code}' "$1" | grep -q '^2'; }
has_route(){ php artisan route:list 2>/dev/null | grep -Eq "$1"; }

# ========== Phase 0: Ground rules ==========
echo -e "\n[Phase 0] Ground rules"
if ls public/assets/*.css >/dev/null 2>&1; then ok "Compiled CSS in public/assets"; else warn "No compiled CSS in public/assets"; fi
if [ -f tools/view_layout_guard.sh ]; then
  bash tools/view_layout_guard.sh >/tmp/_layout.out 2>&1 || true
  if grep -qi 'OUTLIER' /tmp/_layout.out; then warn "Layout guard outliers"; head -n 80 /tmp/_layout.out; else ok "Layout guard clean (public views extend public.layout)"; fi
else
  warn "view_layout_guard.sh not present (strict layout check skipped)"
fi
H=$(curl -s https://www.swaeduae.ae/ | grep -Eio '<meta[^>]+(name|property)=["'\''](description|og:title)["'\'']' | wc -l || true)
[[ $H -ge 1 ]] && ok "Home has meta/OG tags" || warn "Home meta/OG not detected"

# ========== Phase 1: Public site completion ==========
echo -e "\n[Phase 1] Public site"
for u in / /about /privacy /terms /contact /faq /stories /partners; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://swaeduae.ae$u")
  if [ "$u" = "/" ] && [ "$code" = "301" ]; then
    loc=$(curl -sI https://swaeduae.ae/ | tr -d '\r' | awk '/^Location:/ {print $2}')
    echo "/ -> 301 (Location: ${loc:-?})"
    echo "$loc" | grep -q '^https://www\.swaeduae\.ae/' && ok "apex 301 to www" || warn "apex redirects unexpectedly"
  else
    [[ "$code" =~ ^2..$ ]] && ok "$u -> $code" || warn "$u -> $code"
  fi
done
has_route '/opportunities($|/)' && ok "Opportunities routes present" || warn "Opportunities routes missing"
is2xx https://www.swaeduae.ae/opportunities && ok "/opportunities 2xx" || warn "/opportunities not 2xx"

# ========== Phase 2: Volunteer experience ==========
echo -e "\n[Phase 2] Volunteer experience"
for u in /login /register /forgot-password /reset-password /verify-email /my/profile /my/settings; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://www.swaeduae.ae$u")
  [[ "$code" =~ ^2..$|^3..$ ]] && ok "$u reachable ($code)" || warn "$u not reachable ($code)"
done
has_route 'verify-email' && ok "Email verification routes present" || warn "Verify email routes not listed"

# ========== Phase 3: Organization experience ==========
echo -e "\n[Phase 3] Organization portal"
for u in /org/login /org/register /org/dashboard /org/setup; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://www.swaeduae.ae$u")
  [[ "$code" =~ ^2..$|^3..$|^4..$ ]] && ok "$u exists ($code)" || warn "$u not reachable ($code)"
done
php artisan route:list 2>/dev/null | grep -Ei 'org[/ ]|organization' >/dev/null && ok "Org routes exist (route:list)" || warn "Org routes absent in route:list"

# ========== Phase 4: Admin console hardening ==========
echo -e "\n[Phase 4] Admin oversight"
ADM=$(php artisan route:list 2>/dev/null | grep -c 'admin\.swaeduae\.ae' || true)
[[ $ADM -gt 0 ]] && ok "admin.swaeduae.ae domain routes present ($ADM)" || warn "No admin-domain routes"
php artisan route:list 2>/dev/null | awk '/admin\.swaeduae\.ae/ && /(GET|HEAD)[[:space:]]+admin\.swaeduae\.ae\/login|\/logout/' | grep -q . \
  && ok "Admin login/logout on admin domain" || warn "Admin login/logout not detected on admin domain"
PUB_ADMIN=$(php artisan route:list 2>/dev/null | awk '!/admin\.swaeduae\.ae/ && $0 ~ /\/admin(\/|$)/ {print}')
[ -z "$PUB_ADMIN" ] && ok "No /admin routes on public host" || { echo "$PUB_ADMIN"; fail "Public host exposes /admin"; }

# ========== Phase 5: Theme & UX finish ==========
echo -e "\n[Phase 5] Theme & UX"
LAY="resources/views/public/layout.blade.php"
if [ -f "$LAY" ]; then
  if grep -Eiq 'cdn\.tailwindcss\.com|cdnjs|unpkg|bootstrapcdn|ajax\.googleapis\.com' "$LAY"; then warn "CDN references in public layout"; else ok "No CDN refs in public layout"; fi
else
  warn "public.layout missing"
fi

# ========== Phase 6: Stability & security ==========
echo -e "\n[Phase 6] Stability & security"
curl -s -D - -o /dev/null https://www.swaeduae.ae/ | grep -qi 'strict-transport-security' && ok "HSTS present" || warn "HSTS missing"
curl -s -D - -o /dev/null https://www.swaeduae.ae/ | grep -qi '^content-security-policy' && ok "CSP present" || warn "CSP missing"
has_route 'sanctum' && ok "Sanctum routes registered (or inline)" || warn "Sanctum routes not visible"
curl -s -D - -o /dev/null https://www.swaeduae.ae/ | grep -qi '^Set-Cookie: XSRF-TOKEN' && ok "XSRF-TOKEN cookie set" || warn "XSRF-TOKEN not seen on /"
systemctl is-active swaed-queue.service >/dev/null 2>&1 && ok "swaed-queue.service active" || warn "queue service inactive (or no perms)"

# ========== Phase 7: Content & localization ==========
echo -e "\n[Phase 7] Content & i18n"
[ -d lang/en ] && ok "lang/en present" || warn "lang/en missing"
[ -d lang/ar ] && ok "lang/ar present" || warn "lang/ar missing"
grep -RIn --include='*.blade.php' 'dir=.*rtl|rtl' resources/views >/dev/null 2>&1 && ok "RTL markers found" || warn "RTL markers not detected (could be global)"

# ========== Phase 8: QA, UAT & launch ==========
echo -e "\n[Phase 8] QA/UAT & launch"
[ -f .github/workflows/ci-cd.yml ] && ok "CI workflow present" || warn "CI workflow missing"
[ -f .github/workflows/site-quality.yml ] && ok "Site-quality workflow present" || warn "Site-quality workflow missing"
is2xx https://www.swaeduae.ae/robots.txt && ok "robots.txt 2xx" || warn "robots.txt missing"
is2xx https://www.swaeduae.ae/sitemap.xml && ok "sitemap.xml 2xx" || warn "sitemap.xml missing"
[ -f public/manifest.json ] && ok "manifest.json present" || warn "manifest.json missing"
[ -f public/sw.js ] && ok "sw.js present" || warn "sw.js missing"

# Certificates & QR
echo -e "\n[Checks] Certificates & QR"
has_route '/certificates/verify' && ok "Route /certificates/verify listed" || warn "/certificates/verify route not in route:list"
is2xx https://www.swaeduae.ae/certificates/verify && ok "/certificates/verify 2xx" || warn "/certificates/verify not 2xx"
has_route '/api/v1/attendance/heartbeat' && ok "Heartbeat API route exists" || warn "Heartbeat API route missing"

# Backups
echo -e "\n[Ops] Backups"
ls -1 /var/backups/swaeduae 2>/dev/null | tail -n 3 && ok "Backups directory has entries" || warn "Backups dir not found (/var/backups/swaeduae)"

# Summary
echo -e "\n== SUMMARY ==\nPASS=$PASS  WARN=$WARN  FAIL=$FAIL"
echo "Report: $R"
