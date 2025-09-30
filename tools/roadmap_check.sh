#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
# SwaedUAE Roadmap Full Checkup
# - Verifies baseline, Phase 1–2 essentials, and key routes/pages
# - Writes a timestamped report to /public/health/ and prints a URL
set -Eeuo pipefail

BASE_URL="https://swaeduae.ae"
NOW="$(date +%F-%H%M%S)"
OUT="public/health/roadmap-$NOW.txt"
TMP="$(mktemp -d)"
JSON="$TMP/routes.json"

ok()   { printf "OK    - %s\n"   "$1" | tee -a "$OUT"; }
warn() { printf "WARN  - %s\n"   "$1" | tee -a "$OUT"; }
fail() { printf "FAIL  - %s\n"   "$1" | tee -a "$OUT"; }
hdr()  { printf "\n== %s ==\n"    "$1" | tee -a "$OUT"; }

cd /var/www/swaeduae || { echo "Run from project root"; exit 2; }
echo "SwaedUAE Roadmap Checkup  $NOW" | tee "$OUT"
echo "================================================" | tee -a "$OUT"

# --- 0) Environment + guards ---
hdr "Environment"
PHPV="$(php -v | head -n1 || true)"
LARV="$(SENTRY_LARAVEL_DSN='' php artisan --version 2>/dev/null || true)"
NODEV="$(node -v 2>/dev/null || echo 'n/a')"
COMPV="$(composer --version 2>/dev/null || echo 'n/a')"
MEM="$(php -r 'echo ini_get("memory_limit") ?: "n/a";')"
printf "PHP:     %s\nLaravel: %s\nNode:    %s\nComposer:%s\nmemory_limit: %s\n" "$PHPV" "$LARV" "$NODEV" "$COMPV" "$MEM" | tee -a "$OUT"
[[ "$MEM" == "512M" ]] && ok "memory_limit is 512M" || warn "memory_limit is $MEM (target 512M)"

# Temporarily neutralize cached bad Sentry config
SENTRY_LARAVEL_DSN='' php artisan config:clear >/dev/null 2>&1 || true

# --- 1) Baseline checks ---
hdr "Phase 0: Baseline"
# a) Health scripts
[[ -x tools/health.sh ]]      && ok "tools/health.sh present"      || warn "tools/health.sh missing"
[[ -x tools/full_health.sh ]] && ok "tools/full_health.sh present" || warn "tools/full_health.sh missing"
# b) Cron for full health
( crontab -l 2>/dev/null | grep -q "tools/full_health.sh" ) && ok "cron entry for full health found" || warn "cron entry for full health NOT found"
# c) Storage write
touch storage/logs/_roadmap_write_test.log 2>/dev/null && ok "storage writable" || fail "cannot write storage/logs"
rm -f storage/logs/_roadmap_write_test.log 2>/dev/null || true
# d) /qr/verify alias
if SENTRY_LARAVEL_DSN='' php artisan route:list --json > "$JSON" 2>/dev/null; then
  grep -q '"name":"qr.verify"' "$JSON" && ok "route name qr.verify exists" || fail "route name qr.verify missing"
else
  fail "unable to fetch routes JSON (artisan failed)"
fi
# e) Sitemap symlink and HTTP
if [[ -L public/sitemap.xml ]]; then ok "public/sitemap.xml is a symlink"; else warn "public/sitemap.xml is not a symlink"; fi
SCODE="$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/sitemap.xml")"
[[ "$SCODE" == "200" ]] && ok "GET /sitemap.xml -> 200" || warn "GET /sitemap.xml -> $SCODE"

# --- 2) Phase 1: Public site polish ---
hdr "Phase 1: Public Site"
# a) TravelPro layout includes
if grep -q "assets/css" resources/views/public/layout-travelpro.blade.php && grep -q "assets/js" resources/views/public/layout-travelpro.blade.php; then
  ok "TravelPro layout includes CSS/JS"
else
  warn "TravelPro layout may be missing CSS/JS includes"
fi
# b) Header/footer use named routes
HP="resources/views/partials/header-public.blade.php"
FP="resources/views/partials/footer-public.blade.php"
G=0
for n in "home" "opportunities.index" "pages.about" "contact.get"; do
  grep -q "route('$n')" "$HP" 2>/dev/null && G=$((G+1))
done
[[ $G -ge 2 ]] && ok "Header appears to use named routes" || warn "Header may not use named routes"
grep -q "pages.privacy" "$FP" 2>/dev/null && ok "Footer privacy link present" || warn "Footer privacy link missing"
grep -q "pages.terms"   "$FP" 2>/dev/null && ok "Footer terms link present"   || warn "Footer terms link missing"
# c) Public pages HTTP (200 or 302->200)
hdr "Public endpoints"
for u in / /about /faq /contact /opportunities ; do
  code="$(curl -s -o /dev/null -w "%{http_code}" -L "$BASE_URL$u")"
  printf "  %-20s -> %s\n" "$u" "$code" | tee -a "$OUT"
  [[ "$code" == "200" ]] && true || warn "Endpoint $u not 200 (got $code)"
done
# d) PWA
[[ -f public/manifest.json ]]      && ok "manifest.json present"      || warn "manifest.json missing"
[[ -f public/service-worker.js ]]  && ok "service-worker.js present"  || warn "service-worker.js missing"
[[ -f public/icons/icon-192.png ]] && ok "icon-192.png present"       || warn "icon-192.png missing"
[[ -f public/icons/icon-512.png ]] && ok "icon-512.png present"       || warn "icon-512.png missing"
# e) robots.txt
if [[ -f public/robots.txt ]] && grep -qi "sitemap:" public/robots.txt; then ok "robots.txt has sitemap"; else warn "robots.txt missing or lacks sitemap line"; fi
# f) EN/AR lang keys exist
[[ -f resources/lang/en/swaed.php ]] && ok "lang EN swaed.php present" || warn "lang EN swaed.php missing"
[[ -f resources/lang/ar/swaed.php ]] && ok "lang AR swaed.php present" || warn "lang AR swaed.php missing"

# --- 3) Phase 2: Email & queues ---
hdr "Phase 2: Email & Queues"
# a) Zoho SMTP (.env)
grep -q '^MAIL_HOST=smtp.zoho.com' .env && ok "Zoho MAIL_HOST set" || warn "Zoho MAIL_HOST not set"
grep -q '^MAIL_MAILER=smtp' .env && ok "MAIL_MAILER=smtp" || warn "MAIL_MAILER not smtp"
# b) Queues
if grep -q '^QUEUE_CONNECTION=' .env; then
  QC="$(grep '^QUEUE_CONNECTION=' .env | head -n1 | cut -d= -f2)"
  [[ "$QC" != "sync" ]] && ok "QUEUE_CONNECTION=$QC" || warn "QUEUE_CONNECTION is sync (use database/redis)"
else
  warn "QUEUE_CONNECTION not set"
fi
# c) Honeypot/FormRateLimit referenced in Kernel
if grep -q 'Honeypot' app/Http/Kernel.php 2>/dev/null; then ok "Honeypot middleware referenced"; else warn "Honeypot middleware not found in Kernel"; fi
if grep -q 'FormRateLimit' app/Http/Kernel.php 2>/dev/null; then ok "FormRateLimit middleware referenced"; else warn "FormRateLimit middleware not found in Kernel"; fi

# --- 4) Key route names (Volunteer, Org, Admin) ---
hdr "Routes snapshot"
if [[ -s "$JSON" ]]; then
  need_names=( "home" "opportunities.index" "pages.about" "contact.get" "faq" "verify.show" "qr.verify"
               "volunteer.dashboard" "org.dashboard" "admin.opportunities.index" "admin.opportunities" )
  for n in "${need_names[@]}"; do
    if grep -q "\"name\":\"$n\"" "$JSON"; then ok "route $n"; else fail "route $n missing"; fi
  done
else
  warn "Route JSON not available; skipping name checks"
fi

# --- 5) Security posture ---
hdr "Security"
grep -q '^APP_DEBUG=false' .env && ok "APP_DEBUG=false" || warn "APP_DEBUG not false"
# Sentry DSN sanity (warn if placeholder)
if grep -q '^SENTRY_LARAVEL_DSN=' .env; then
  DSN="$(grep '^SENTRY_LARAVEL_DSN=' .env | head -n1 | cut -d= -f2-)"
  [[ "$DSN" =~ ^https:// ]] && ok "SENTRY DSN set" || warn "SENTRY DSN looks invalid/placeholder (disable or set real)"
else
  ok "SENTRY DSN not set (OK if intentionally disabled)"
fi

# --- 6) Summary & URL ---
hdr "Summary"
echo "Report written to: $OUT" | tee -a "$OUT"
echo "URL: $BASE_URL/${OUT#public/}" | tee -a "$OUT"
printf "\n" | tee -a "$OUT"
