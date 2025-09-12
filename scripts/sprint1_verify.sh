#!/bin/bash
set -e
cd /home3/vminingc/swaeduae.ae/laravel-app
echo "=== Sprint 1 Verify ==="

if grep -qE "Illuminate\\\Support\\\Facades\\\Rp|Applicware|php artisan config:cache" routes/web.php; then
  echo "[ALERT] routes/web.php looks corrupted. Consider running the fix script."
fi

php -l app/Http/Controllers/Admin/ApplicationReviewController.php
php -l app/Http/Controllers/Admin/AttendanceController.php

php artisan route:list | grep -E "admin/applications|admin/opportunities/.*/attendance|admin/opportunities/.*/scan" || echo "[WARN] Expected routes not found."

MYSQL="mysql -u vminingc_admin -p'' -N -e"
$MYSQL "USE vminingc_swaeduae_db; SHOW TABLES LIKE 'applications'; SHOW TABLES LIKE 'attendances';"
$MYSQL "USE vminingc_swaeduae_db; SHOW COLUMNS FROM opportunities LIKE 'capacity'; SHOW COLUMNS FROM opportunities LIKE 'waitlist_enabled';"
$MYSQL "USE vminingc_swaeduae_db; SHOW COLUMNS FROM volunteer_hours LIKE 'minutes'; SHOW COLUMNS FROM volunteer_hours LIKE 'notes'; SHOW COLUMNS FROM volunteer_hours LIKE 'source'; SHOW COLUMNS FROM volunteer_hours LIKE 'opportunity_id';"

echo "---- storage/logs/laravel.log (last 50 lines) ----"
tail -n 50 storage/logs/laravel.log || true
echo "=== Verify done ==="
