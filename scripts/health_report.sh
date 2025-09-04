#!/usr/bin/env bash
set -euo pipefail
echo "=== PHP ==="; php -v | head -n1
echo "=== Laravel ==="; php artisan --version || true
echo "=== Environment ==="; php artisan about --only=environment || true
echo "=== Cache (clear) ==="; php artisan optimize:clear || true
echo "=== Routes (admin count) ==="; php artisan route:list --path=admin 2>/dev/null | wc -l || true
echo "=== Storage perms ==="; ls -ld storage bootstrap/cache || true
echo "=== Error views ==="; ls -1 resources/views/errors | sed 's/^/ - /' || true
echo "=== Done ==="
