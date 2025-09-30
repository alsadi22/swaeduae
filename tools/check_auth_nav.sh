#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -Eeuo pipefail
cd /var/www/swaeduae
echo "=== Auth/nav sanity @ $(date -Is) ==="

echo "-- Key routes --"
php artisan route:list | grep -E "login|register|logout|my/profile|password\.reset|password\.store" || true

echo "-- Dropdown partial (first 80 lines) --"
nl -ba resources/views/partials/auth_dropdown.blade.php | sed -n '1,80p'

echo "-- HTTP 200 checks --"
for u in / /login /register /forgot-password; do
  printf " https://swaeduae.ae%-18s -> %s\n" "$u" "$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u")"
done

echo "-- CSRF presence --"
for u in /login /register /forgot-password; do
  if curl -s "https://swaeduae.ae$u" | grep -q 'name="_token"'; then
    echo " $u: CSRF OK"
  else
    echo " $u: CSRF MISSING"
  fi
done

echo "-- HOME constant --"
grep -n "public const HOME" app/Providers/RouteServiceProvider.php || echo "HOME constant not found"

echo "=== Done ==="
