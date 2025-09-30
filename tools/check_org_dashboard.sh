#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
php tools/check_org_dashboard.php || true
echo
echo "Recent relevant log lines:"
( tail -n 300 "storage/logs/laravel-$(date +%F).log" 2>/dev/null || tail -n 300 storage/logs/laravel.log 2>/dev/null ) \
 | egrep -i 'org|dashboard|Undefined|variable|Trying|exception|View' | tail -n 30 || true
