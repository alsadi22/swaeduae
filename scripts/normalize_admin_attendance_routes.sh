#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup routes/admin.php ..."
cp -f routes/admin.php "routes/admin.php.$TS.bak" || true

# 1) Clean any duplicate '<?php' tags and BOM
sed -i '1s/^\xEF\xBB\xBF//' routes/admin.php || true
awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' routes/admin.php > routes/admin.php.tmp
mv routes/admin.php.tmp routes/admin.php

# 2) Ensure correct use line once
sed -i '/^use AppHttpControllersAdminAttendanceController;$/d' routes/admin.php || true
if ! grep -q '^use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php; then
  sed -i '1a use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php
fi

# 3) Remove ALL prior definitions of these names/URIs (we will re-add canonical ones)
#   - names: opportunities.attendance, opportunities.attendance.update, opportunities.finalize.issue, certificates.resend
#   - any stray URI variants using {id} for attendance index
sed -i "/name(['\"]opportunities\.attendance['\"])/d" routes/admin.php
sed -i "/name(['\"]opportunities\.attendance\.update['\"])/d" routes/admin.php
sed -i "/name(['\"]opportunities\.finalize\.issue['\"])/d" routes/admin.php
sed -i "/name(['\"]certificates\.resend['\"])/d" routes/admin.php
# delete any explicit GET route lines to /opportunities/{id}/attendance
sed -i "/Route::get(['\"]\/\?opportunities\/{id}\/attendance['\"]/d" routes/admin.php
sed -i "/Route::get(['\"]opportunities\/{id}\/attendance['\"]/d" routes/admin.php

# 4) Append a single canonical group for AttendanceController (AdminMiddleware class style)
cat >> routes/admin.php <<'PHP'

/* === Canonical Attendance routes (normalized, single source of truth) === */
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('/opportunities/{opportunity}/attendance', [AttendanceController::class, 'index'])->name('opportunities.attendance');
        Route::post('/opportunities/{opportunity}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('opportunities.attendance.update');
        Route::post('/opportunities/{opportunity}/finalize-issue', [AttendanceController::class, 'finalizeIssue'])->name('opportunities.finalize.issue');
        Route::post('/certificates/{certificate}/resend', [AttendanceController::class, 'resendCertificate'])->name('certificates.resend');
    });
PHP

echo "==> Lint routes..."
php -l routes/admin.php

echo "==> Clear/rebuild caches..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Run migrations..."
php artisan migrate --force

echo "==> Done. If needed, restore from routes/admin.php.$TS.bak"
