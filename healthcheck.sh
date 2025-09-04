#!/usr/bin/env bash
set -Eeuo pipefail
BASE_URL="${1:-https://swaeduae.ae}"
echo "=== SWAE Laravel Healthcheck @ $(date -Is) ==="
echo "Base URL: $BASE_URL"
echo

sec(){ printf "\n-- %s --\n" "$1"; }

# 0) Guard: must be Laravel root
test -f artisan || { echo "Run this from your Laravel app root (artisan not found)"; exit 1; }

sec "System & PHP"
(php -v | head -n1) || true
which php || true
(composer -V) 2>/dev/null || echo "composer: not found (ok if unavailable)"
echo "Extensions (common):"
php -m 2>/dev/null | egrep -i 'mbstring|openssl|pdo|mysql|curl|gd|imagick|fileinfo|bcmath|intl|zip' | sort || true
echo
echo "Disk:"
df -h . | awk 'NR==1||NR==2'
echo
echo "Memory (from /proc):"
awk '/MemTotal|MemAvailable|SwapTotal|SwapFree/ {print}' /proc/meminfo 2>/dev/null || true

sec "Laravel App Basics"
php artisan --version || true
php artisan env || true
echo
echo "about (env, cache, drivers):"
php artisan about --only=environment,cache,drivers,app 2>/dev/null || php artisan about 2>/dev/null || true
echo
echo "Routes count:"
php artisan route:list | wc -l || true

sec "Database & Migrations"
php artisan tinker --execute="echo DB::select('select 1 as ok')[0]->ok, PHP_EOL;" 2>/dev/null || echo "DB quick check: (tinker failed or DB not reachable)"
php artisan migrate:status --no-interaction || true

sec "Queue & Scheduler"
php artisan queue:failed --no-interaction || true
php artisan schedule:list 2>/dev/null || echo "schedule:list not available (ok on older versions)"

sec "Storage & Permissions"
printf "public/storage symlink: "; [ -L public/storage ] && echo "OK" || echo "MISSING"
for d in storage bootstrap/cache; do
  printf "%-20s : " "$d"
  if [ -w "$d" ]; then
    t="$(mktemp -p "$d" .permtest.XXXX 2>/dev/null || true)"; [ -n "${t:-}" ] && rm -f "$t"
    echo "writable"
  else
    echo "NOT writable"
  fi
done

sec "TLS Certificate"
if command -v openssl >/dev/null 2>&1; then
  host="$(echo "$BASE_URL" | sed -E 's#^https?://([^/]+)/?.*$#\1#')"
  echo | openssl s_client -servername "$host" -connect "$host:443" 2>/dev/null | openssl x509 -noout -dates -issuer -subject || echo "OpenSSL check failed"
else
  echo "openssl not installed; skipping"
fi

sec "HTTP Smoke (status,time)"
endpoints='
/
 /events
 /opportunities
 /organizations
 /gallery
 /sitemap.xml
 /robots.txt
 /login
 /org/login
 /org/dashboard
 /up
'
while read -r p; do
  [ -z "$p" ] && continue
  code_time=$(curl -sS -o /dev/null -w "%{http_code} %{time_total}\n" "$BASE_URL$p" || echo "000 0")
  printf "%-28s -> %s\n" "$p" "$code_time"
done <<< "$endpoints"

sec "Recent Application Logs"
log="storage/logs/laravel.log"
if [ -f "$log" ]; then
  echo "Size: $(du -h "$log" | awk '{print $1}'), Updated: $(date -r "$log" '+%F %T')"
  echo "--- last 120 lines (errors first if any) ---"
  (grep -E "ERROR|CRITICAL" "$log" | tail -n 40; echo; tail -n 120 "$log") 2>/dev/null || tail -n 120 "$log"
else
  echo "No laravel.log found (ok if empty or rotated)."
fi

echo
echo "=== Healthcheck complete @ $(date -Is) ==="
