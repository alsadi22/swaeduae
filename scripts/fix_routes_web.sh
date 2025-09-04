#!/bin/bash
set -e
cd /home3/vminingc/swaeduae.ae/laravel-app
TS=$(date +%Y%m%d_%H%M%S)

# Backup current routes/web.php
cp routes/web.php "routes/web.php.${TS}.pre_fix.bak"

# Prefer restoring from the newest .bak we created earlier (if available)
LATEST_BAK=$(ls -1t routes/web.php.*.bak 2>/dev/null | head -n1 || true)
if [ -n "$LATEST_BAK" ]; then
  cp "$LATEST_BAK" routes/web.php
  echo "[Fix] Restored routes/web.php from $LATEST_BAK"
else
  # Surgical cleanup if no .bak exists
  sed -i "/Illuminate\\\\Support\\\\Facades\\\\Rp/d" routes/web.php
  sed -i "/Applicware/d" routes/web.php
  sed -i "/php artisan/d" routes/web.php
  sed -i "/Route::post('\/attendance\/check-in'/d" routes/web.php
  sed -i "/printf .*SPRINT 1 routes include/d" routes/web.php
  echo "[Fix] Cleaned suspicious lines in routes/web.php"
fi

# Ensure sprint1 include exists
if ! grep -q "routes/sprint1.php" routes/web.php; then
  printf "\n// SPRINT 1 routes include (safe)\nif (file_exists(base_path('routes/sprint1.php'))) { require base_path('routes/sprint1.php'); }\n" >> routes/web.php
  echo "[Fix] Added include for routes/sprint1.php"
fi

# Clear caches and show routes
php artisan route:clear && php artisan config:clear && php artisan cache:clear >/dev/null 2>&1 || true
php artisan config:cache >/dev/null 2>&1 || true
php artisan route:list | grep -E "admin/applications|admin/opportunities/.*/attendance|admin/opportunities/.*/scan" || true
echo "[Fix] Done."
