#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup routes/admin.php"
cp -f routes/admin.php "routes/admin.php.parsefix.$TS.bak" || true

# Normalize file: remove BOM, CRLF, duplicate <?php
sed -i '1s/^\xEF\xBB\xBF//' routes/admin.php || true
sed -i 's/\r$//' routes/admin.php || true
awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' routes/admin.php > routes/admin.php.tmp && mv routes/admin.php.tmp routes/admin.php

# Remove any broken/incomplete Attendance route lines (the "->;" type and partial lines)
sed -i -E "/->\s*;$/d" routes/admin.php || true
sed -i -E "/AttendanceController::class/d" routes/admin.php || true

# Ensure the correct use statement exists exactly once
# 1) delete any malformed variants
sed -i "/^use AppHttpControllersAdminAttendanceController;$/d" routes/admin.php || true
# 2) delete duplicates of the correct one and reinsert once after <?php
sed -i "/^use App\\\Http\\\Controllers\\\Admin\\\AttendanceController;$/d" routes/admin.php || true
sed -i '1a use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php

# Append ONE clean canonical block
cat >> routes/admin.php <<'PHP'

/* === Canonical Attendance routes (single source of truth) === */
Route::middleware(['web','auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')->as('admin.')->group(function () {
        Route::get('/opportunities/{opportunity}/attendance', [AttendanceController::class, 'index'])->name('opportunities.attendance');
        Route::post('/opportunities/{opportunity}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('opportunities.attendance.update');
        Route::post('/opportunities/{opportunity}/finalize-issue', [AttendanceController::class, 'finalizeIssue'])->name('opportunities.finalize.issue');
        Route::post('/certificates/{certificate}/resend', [AttendanceController::class, 'resendCertificate'])->name('certificates.resend');
    });
PHP

echo "==> Lint routes/admin.php"
php -l routes/admin.php

echo "==> Clear & rebuild caches"
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Quick sanity check"
php artisan route:list | grep -E "opportunities\.attendance|finalize-issue" || true

echo "==> Done. Backup: routes/admin.php.parsefix.$TS.bak"
