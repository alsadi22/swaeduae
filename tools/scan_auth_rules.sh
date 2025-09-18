#!/usr/bin/env bash
ts(){ date +%Y%m%d_%H%M%S; }; TS=$(ts)
OUT=~/swaed_scans/reports/auth_rules_$TS.txt
echo "=== SwaedUAE Auth Rules Scan @ $TS ===" | tee "$OUT"

echo -e "\n== ROUTES: Volunteer & Org auth ==" | tee -a "$OUT"
php artisan route:list 2>/dev/null | egrep -i '(^|[[:space:]])/(login|register|forgot-password|verify-email|auth/google)\b|(^|[[:space:]])org/(login|register)\b' \
  | tee -a "$OUT"

echo -e "\n== /my/* require 'verified' (via code or group) ==" | tee -a "$OUT"
grep -RInE "Route::middleware\(\s*\[.*'verified'.*\]\s*\).*group" routes app/Http 2>/dev/null | tee -a "$OUT"

echo -e "\n== SOCIAL LOGIN presence ==" | tee -a "$OUT"
grep -q '"laravel/socialite"' composer.json && echo "composer.json: laravel/socialite FOUND" | tee -a "$OUT" || echo "composer.json: laravel/socialite NOT FOUND" | tee -a "$OUT"
php artisan route:list 2>/dev/null | egrep -i '/auth/google|social\.redirect|social\.callback' | tee -a "$OUT"
[ -f config/services.php ] && awk '/google|facebook|apple/{print FILENAME":"NR": "$0}' config/services.php | tee -a "$OUT"
[ -f .env ] && { echo "-- .env provider keys (redacted) --" | tee -a "$OUT"; egrep -n '^(GOOGLE_CLIENT_ID|GOOGLE_CLIENT_SECRET)=' .env | sed 's/=.*/=<set>/' | tee -a "$OUT"; }

echo -e "\n== ORG SIGNUP business email enforcement ==" | tee -a "$OUT"
# Confirm rule/middleware files exist
ls -l app/Rules/CompanyEmailDomain.php 2>/dev/null | tee -a "$OUT"
ls -l app/Http/Middleware/BusinessEmailOnly.php 2>/dev/null | tee -a "$OUT"
# Look for usage in Org registration flow
grep -RInE "CompanyEmailDomain|BusinessEmailOnly|gmail\.com|yahoo\.com|outlook\.com|hotmail\.com|proton" app/Http/Controllers app/Http/Requests routes 2>/dev/null | tee -a "$OUT"

echo -e "\n== HTTP probes (expect 200 for public auth pages) ==" | tee -a "$OUT"
probe(){ local url="$1"; local code loc; code=$(curl -s -o /dev/null -w "%{http_code}" "$url"); loc=$(curl -s -I "$url" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}' | tr -d "\r"); printf "%-34s -> %s %s\n" "$url" "$code" "${loc:+($loc)}"; }
for u in /login /register /forgot-password /verify-email /auth/google /org/login /org/register; do
  probe "https://swaeduae.ae$u" | tee -a "$OUT"
done

echo -e "\n== SUMMARY ==" | tee -a "$OUT"
php artisan route:list 2>/dev/null | egrep -qi '/auth/google|social\.redirect|social\.callback' && echo "Volunteer social login: ROUTES PRESENT" | tee -a "$OUT" || echo "Volunteer social login: ROUTES MISSING" | tee -a "$OUT"
[ -f app/Rules/CompanyEmailDomain.php ] || [ -f app/Http/Middleware/BusinessEmailOnly.php ] && \
  echo "Org business email enforcement: FOUND (rule/middleware present)" | tee -a "$OUT" || \
  echo "Org business email enforcement: MISSING" | tee -a "$OUT"
grep -Rqi "Route::middleware\(\s*\[.*'verified'.*\]" routes app/Http 2>/dev/null && \
  echo "/my/* requires verified: YES" | tee -a "$OUT" || echo "/my/* requires verified: CHECK" | tee -a "$OUT"

echo "Report saved: $OUT" | tee -a "$OUT"
