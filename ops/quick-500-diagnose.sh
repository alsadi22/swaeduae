#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")/.."

echo "== PHP =="
php -v || true
php -m | tr '\n' ' ' | sed 's/ \{1,\}/, /g' | sed 's/^/Extensions: /'; echo

echo -e "\n== Paths & permissions =="
ls -ld storage bootstrap/cache || true
find storage bootstrap/cache -maxdepth 1 -type d -printf "%M %u:%g  %p\n" 2>/dev/null || true

echo -e "\n== Autoload present? =="
[ -f vendor/autoload.php ] && echo "OK: vendor/autoload.php exists" || echo "MISSING: vendor/autoload.php"

echo -e "\n== .env sanity (key envs) =="
grep -E '^(APP_ENV|APP_DEBUG|APP_URL|APP_KEY)=' .env || true

echo -e "\n== Laravel log (last 150 lines) =="
tail -n 150 storage/logs/laravel.log 2>/dev/null || echo "No laravel.log yet"

echo -e "\n== Web server error_log (last 100 lines if available) =="
if ls ~/logs/*error*log >/dev/null 2>&1; then
  tail -n 100 ~/logs/*error*log || true
else
  tail -n 100 error_log 2>/dev/null || true
  tail -n 100 public/error_log 2>/dev/null || true
fi

echo -e "\n== Artisan about (may fail; failure is a clue) =="
php artisan about --no-ansi || true

echo -e "\n== Run app via CLI to reveal stacktrace (not public) =="
php -d display_errors=1 public/index.php 2>&1 | tail -n 120 || true
