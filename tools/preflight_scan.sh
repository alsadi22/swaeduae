#!/usr/bin/env bash
set -Eeuo pipefail
echo "== Preflight Scan =="

echo "-- PHP version --"; php -v | head -n1 || true
echo "-- Node version --"; node -v 2>/dev/null || true

echo "-- PHP lint (app/, routes/, config/) --"
git ls-files '*.php' ':!:_archive/**' ':!:_audits/**' ':!:_quarantine/**' ':!:legacy/**' \
| grep -E '^(app/|routes/|config/)' \
| xargs -r -n1 php -l | grep -v "No syntax errors" || echo "PHP lint OK"

echo "-- Blade extends sanity (no unquoted) --"
grep -R --line-number "@extends(public\.layout" resources/views/public || echo "Extends OK"

echo "-- Routes (top 50) --"
php artisan route:list | sed -n "1,120p"

echo "-- Cache status --"
php artisan about --only=environment,cache || true
