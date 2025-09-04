#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup entire routes/ directory ..."
cp -a routes "routes.fullbak.$TS"

# Helper: delete lines matching a regex from a file if it exists
del_lines () {
  local f="$1"; local pat="$2"
  [ -f "$f" ] || return 0
  # Remove BOM & duplicate <?php
  sed -i '1s/^\xEF\xBB\xBF//' "$f" || true
  awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' "$f" > "$f.tmp" && mv "$f.tmp" "$f"
  # Delete lines matching pattern
  sed -i -E "/$pat/d" "$f" || true
}

echo "==> Purge duplicate attendance route names across all route files ..."
for f in routes/*.php; do
  # Names (with or without 'admin.' prefix)
  del_lines "$f" "name\((\"|')admin\.opportunities\.attendance(\.update)?(\"|')\)"
  del_lines "$f" "name\((\"|')opportunities\.attendance(\.update)?(\"|')\)"
  del_lines "$f" "name\((\"|')admin\.opportunities\.finalize\.issue(\"|')\)"
  del_lines "$f" "name\((\"|')opportunities\.finalize\.issue(\"|')\)"
  del_lines "$f" "name\((\"|')admin\.certificates\.resend(\"|')\)"
  del_lines "$f" "name\((\"|')certificates\.resend(\"|')\)"

  # Old URI variants that might carry those names elsewhere
  del_lines "$f" "Route::get\((\"|')/?admin/opportunities/\{id\}/attendance(\"|')"
  del_lines "$f" "Route::get\((\"|')/?opportunities/\{id\}/attendance(\"|')"
  del_lines "$f" "Route::get\((\"|')/?opportunities/\{opportunity\}/attendance(\"|')"
done

echo "==> Remove broken leftover lines in routes/admin.php ..."
# Remove any half-broken lines like '->;' left by earlier sed
sed -i -E "/->\s*;$/d" routes/admin.php || true
# Remove any AttendanceController route lines to avoid duplicates before we re-add
sed -i -E "/AttendanceController::class/d" routes/admin.php || true

echo "==> Ensure correct use statement for AttendanceController ..."
grep -q '^use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php || \
  sed -i '1a use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php

echo "==> Append ONE canonical Attendance route group ..."
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

echo "==> Lint files ..."
php -l routes/admin.php
php -l routes/web.php || true
[ -f routes/sprint1.php ] && php -l routes/sprint1.php || true

echo "==> Clear & rebuild caches ..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Run migrations ..."
php artisan migrate --force

echo "==> Done. Backups at routes.fullbak.$TS"
