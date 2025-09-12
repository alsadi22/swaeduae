#!/bin/bash
set -euo pipefail

echo "==> Verifying Laravel root..."
test -f artisan || { echo "Run this from your Laravel root (artisan not found)"; exit 1; }

STAMP="$(date +%F-%H%M%S)"
BACKUP_DIR="_routes_misplaced_backup_${STAMP}"
mkdir -p "$BACKUP_DIR"

echo "==> Backup current routes/web.php (if exists)"
if [ -f routes/web.php ]; then
  cp routes/web.php "routes/web.php.bak.${STAMP}"
fi

echo "==> Writing clean routes/web.php"
cat > routes/web.php << 'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;

// --- Public pages (named) ---
Route::view('/', 'welcome')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/faq', 'faq')->name('faq');

// --- Auth routes split file (already defined in routes/auth.php) ---
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// --- Admin group ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);
    // Custom toggle route used by the Blade view
    Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
});
PHP

echo "==> Quarantining stray files living under routes/ (should not be there)"
if [ -d routes/app ]; then
  mv routes/app "${BACKUP_DIR}/routes_app"
  echo "   moved routes/app -> ${BACKUP_DIR}/routes_app"
fi
if [ -d routes/resources ]; then
  mv routes/resources "${BACKUP_DIR}/routes_resources"
  echo "   moved routes/resources -> ${BACKUP_DIR}/routes_resources"
fi
if [ -f web.php ]; then
  mv web.php "${BACKUP_DIR}/web.php"
  echo "   moved ./web.php -> ${BACKUP_DIR}/web.php"
fi

echo "==> Clearing caches"
php artisan route:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan optimize || true

echo "==> Route checks (should list these):"
php artisan route:list --name=about --name=contact --name=faq --name=admin.users.toggle || true

echo "==> Done."
echo "   - Clean routes written to routes/web.php"
echo "   - Stray files backed up in: ${BACKUP_DIR}"
