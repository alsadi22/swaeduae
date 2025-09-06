#!/usr/bin/env bash
set -euo pipefail
APP_BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_BASE"

echo "== QUICK HEALTH =="
php artisan about || true
php artisan route:list --path=admin | head -n 60 || true
echo "-- HTTP probes --"
for u in / /about /privacy /terms /org/login /admin/login; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u")
  echo "$u -> $code"
done
