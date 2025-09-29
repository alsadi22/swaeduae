#!/usr/bin/env bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
cd "$APP"

# Laravel caches
/usr/local/bin/php artisan config:cache >/dev/null || true
/usr/local/bin/php artisan route:cache  >/dev/null || true
/usr/local/bin/php artisan view:cache   >/dev/null || true

# Prime microcache (Arabic & English toggles used on site)
BASE="https://swaeduae.ae"
for url in "$BASE/" "$BASE/?lang=ar" "$BASE/?lang=en"; do
  curl -s -D- -o /dev/null "$url" >/dev/null || true
done
echo "[OK] warm done"
