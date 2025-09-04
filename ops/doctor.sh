#!/usr/bin/env bash
set -euo pipefail
OUT="/tmp/laravel-doctor.txt"
BASE="$(pwd)"

say(){ printf "\n=== %s ===\n" "$1" | tee -a "$OUT"; }

: > "$OUT"
say "BASIC"
php -v 2>&1 | head -n1 | tee -a "$OUT"
php artisan --version 2>&1 | tee -a "$OUT"
date | tee -a "$OUT"

say ".ENV SNAPSHOT (core)"
egrep -n '^(APP_ENV|APP_DEBUG|APP_URL|SESSION_DRIVER|SESSION_DOMAIN|CACHE_DRIVER|QUEUE_CONNECTION|DB_CONNECTION|DB_HOST|DB_DATABASE|MAIL_MAILER)=' .env | tee -a "$OUT" || true

say "PERMISSIONS"
for d in storage bootstrap/cache storage/framework/sessions; do
  printf "%-32s " "$d" | tee -a "$OUT"
  test -w "$d" && echo "OK (writable)" | tee -a "$OUT" || echo "FAIL (not writable)" | tee -a "$OUT"
done

say "PHP EXTENSIONS (must-have)"
php -m | egrep -i 'pdo|mbstring|openssl|json|ctype|tokenizer|xml|fileinfo|curl' | sort -u | tee -a "$OUT"

say "ARTISAN ABOUT"
php artisan about 2>&1 | tee -a "$OUT" || true

say "ROUTES — counts & key auth"
php artisan route:list | wc -l | xargs -I{} echo "Total routes: {}" | tee -a "$OUT"
php artisan route:list --name=login --name=register --name=logout --name=profile 2>&1 | tee -a "$OUT"

say "ROUTE NAME CONSISTENCY (views -> routes)"
# Wanted names from blades
grep -RIn "route('.*')" resources/views \
  | sed -E "s/.*route\('([^']+)'.*/\1/" | sort -u > /tmp/wants.txt
# Names that exist
php artisan route:list \
  | awk 'NR>2 && $0 !~ /Name/ && $0 !~ /^+/{print $NF}' \
  | sort -u > /tmp/has.txt
comm -23 /tmp/wants.txt /tmp/has.txt | tee /tmp/missing-names.txt | sed 's/^/MISSING: /' | tee -a "$OUT"
MISSING=$(wc -l < /tmp/missing-names.txt || echo 0)
echo "Missing count: $MISSING" | tee -a "$OUT"

say "VIEWS — critical exist"
for f in resources/views/auth/login.blade.php \
         resources/views/auth/register.blade.php \
         resources/views/volunteer/profile.blade.php \
         resources/views/profile/show.blade.php \
         resources/views/layouts/app.blade.php; do
  test -f "$f" && echo "OK $f" | tee -a "$OUT" || echo "MISSING $f" | tee -a "$OUT"
done

say "ASSETS — build artifact present"
if [ -f public/build/manifest.json ] || [ -f public/mix-manifest.json ]; then
  echo "OK: asset manifest present" | tee -a "$OUT"
else
  echo "WARN: no Vite/Mix manifest found (CSS/JS may be unstyled)" | tee -a "$OUT"
fi

say "DB — migration status (tests connection)"
php artisan migrate:status 2>&1 | tee -a "$OUT" || echo "FAIL: migrate:status error" | tee -a "$OUT"

say "HTTP SMOKE (public endpoints)"
HOST="https://swaeduae.ae"
for u in / /login /register /login?type=organization /register?type=organization \
         /org/login /organization/login /volunteer/login /profile /volunteer/profile /my/certificates /my/hours; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "$HOST$u")
  echo "$u -> $code" | tee -a "$OUT"
done

say "LOG TAIL (newest 200 lines)"
tail -n 200 storage/logs/laravel.log 2>/dev/null | tee -a "$OUT" || echo "(no log yet)" | tee -a "$OUT"

echo -e "\n---\nReport saved to: $OUT"
