#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup routes/admin.php ..."
cp -f routes/admin.php "routes/admin.php.$TS.bak" || true

# Remove BOM if present
sed -i '1s/^\xEF\xBB\xBF//' routes/admin.php || true

# Remove any malformed 'use AppHttpControllersAdminAttendanceController;' lines
sed -i '/^use AppHttpControllersAdminAttendanceController;$/d' routes/admin.php || true

# Keep only the first '<?php' opening tag
awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' routes/admin.php > routes/admin.php.tmp
mv routes/admin.php.tmp routes/admin.php

# Ensure the correct use statement exists exactly once
if ! grep -q '^use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php; then
  # Insert right after the opening <?php
  sed -i '1a use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php
fi

echo "==> Lint routes/admin.php ..."
php -l routes/admin.php

echo "==> Clear/rebuild caches ..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Run migrations ..."
php artisan migrate --force

echo "==> Done. Backup: routes/admin.php.$TS.bak"
