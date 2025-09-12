#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup routes directory ..."
cp -a routes "routes.bak.$TS"

# Helper to remove duplicate lines by pattern from a file if it exists
remove_patterns () {
  local file="$1"; shift
  [ -f "$file" ] || return 0
  # Remove BOM
  sed -i '1s/^\xEF\xBB\xBF//' "$file" || true
  # Keep only FIRST '<?php'
  awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' "$file" > "$file.tmp" && mv "$file.tmp" "$file"
  # Apply deletions
  for p in "$@"; do
    sed -i -E "s/$p//g" "$file"
    sed -i -E "/$p/d" "$file"
  done
}

echo "==> Remove ALL existing definitions of attendance routes/names across routes/*.php ..."
for f in routes/*.php; do
  remove_patterns "$f" \
    "name\(['\"]opportunities\.attendance['\"]\)" \
    "name\(['\"]opportunities\.attendance\.update['\"]\)" \
    "name\(['\"]opportunities\.finalize\.issue['\"]\)" \
    "name\(['\"]certificates\.resend['\"]\)" \
    "name\(['\"]admin\.opportunities\.attendance['\"]\)" \
    "name\(['\"]admin\.opportunities\.attendance\.update['\"]\)" \
    "name\(['\"]admin\.opportunities\.finalize\.issue['\"]\)" \
    "name\(['\"]admin\.certificates\.resend['\"]\)" \
    "Route::get\(['\"]/??admin\/opportunities\/\{id\}\/attendance['\"]" \
    "Route::get\(['\"]/??opportunities\/\{id\}\/attendance['\"]" \
    "Route::get\(['\"]/??opportunities\/\{opportunity\}\/attendance['\"]"
done

echo "==> Ensure correct 'use' once in routes/admin.php ..."
grep -q '^use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php || \
  sed -i '1a use App\\Http\\Controllers\\Admin\\AttendanceController;' routes/admin.php

echo "==> Append canonical Attendance routes if missing ..."
if ! grep -q "opportunities\\.attendance';" routes/admin.php; then
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
fi

echo "==> Lint route files ..."
php -l routes/admin.php
php -l routes/web.php || true

echo "==> Clear & rebuild caches ..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Run migrations ..."
php artisan migrate --force

echo "==> OK. Backups at routes.bak.$TS"
