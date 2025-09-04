#!/usr/bin/env bash
set -euo pipefail
APP_ROOT="${APP_ROOT:-$( [ -f artisan ] && echo . || echo laravel-app )}"
cd "$APP_ROOT"

echo "=== ROUTES touching /profile ==="
php artisan route:list | awk -v FS='[|]' '/^\|/{
  for(i=1;i<=NF;i++) gsub(/^[ \t]+|[ \t]+$/, "", $i);
  if($3=="/profile" || $4=="profile" || $3=="volunteer/profile" || $4 ~ /(volunteer\.profile|profile\.)/) {
    print $0
  }
}'

echo
echo "=== Route definitions referencing '/profile' in routes/*.php ==="
grep -RIn "['\"]/profile['\"]" routes || true
grep -RIn "->name\(['\"]profile['\"]\)" routes || true

echo
echo "=== Compile all views to catch template/includes errors ==="
php artisan view:clear >/dev/null 2>&1 || true
if php artisan view:cache 2> /tmp/viewcache.err; then
  echo "View compile: OK"
else
  echo "View compile: FAILED (last lines):"
  tail -n 80 /tmp/viewcache.err
fi

echo
echo "=== Last 120 lines of storage/logs/laravel.log (if any) ==="
tail -n 120 storage/logs/laravel.log || true

echo
echo "=== Quick presence check for profile blades ==="
ls -la resources/views/profile 2>/dev/null || echo "(no resources/views/profile directory found)"
