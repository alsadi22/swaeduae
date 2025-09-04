#!/usr/bin/env bash
set -euo pipefail

ROOT="/home3/vminingc/swaeduae.ae/laravel-app"
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Back up route files..."
cp -f routes/web.php "routes/web.php.$TS.bak" || true
[ -f routes/admin.php ] && cp -f routes/admin.php "routes/admin.php.$TS.bak" || true

fix_duplicate_php_tags () {
  local file="$1"
  # Remove BOM if present
  sed -i '1s/^\xEF\xBB\xBF//' "$file" || true
  # Keep only the first '<?php' opening tag
  awk 'BEGIN{seen=0} { if ($0 ~ /^<\?php/) { if (seen==1) {next} else {seen=1; print; next} } print }' "$file" > "$file.tmp"
  mv "$file.tmp" "$file"
}

echo "==> Clean duplicate <?php tags..."
fix_duplicate_php_tags routes/web.php
[ -f routes/admin.php ] && fix_duplicate_php_tags routes/admin.php || true

echo "==> Fix malformed use statement in routes/web.php (if present)..."
# Replace the broken line if it exists
sed -i "s/^use AppHttpControllersVolunteerProfileController;/use App\\\\Http\\\\Controllers\\\\Volunteer\\\\ProfileController;/" routes/web.php || true

echo "==> Ensure correct 'use' exists once..."
if ! grep -q "use App\\\\Http\\\\Controllers\\\\Volunteer\\\\ProfileController;" routes/web.php; then
  # Insert after the first line (which should be '<?php')
  sed -i '1a use App\\Http\\Controllers\\Volunteer\\ProfileController;' routes/web.php
fi

echo "==> Ensure volunteer profile route group exists once..."
if ! grep -q "Route::get('/volunteer/profile" routes/web.php; then
  cat >> routes/web.php <<'PHP'

Route::middleware(['web','auth','verified'])->group(function () {
    Route::get('/volunteer/profile/{tab?}', [\App\Http\Controllers\Volunteer\ProfileController::class, 'index'])
        ->where('tab', 'overview|hours|events|applications|certificates')
        ->name('volunteer.profile');
});
PHP
fi

echo "==> Lint routes..."
php -l routes/web.php || { echo "PHP lint failed on routes/web.php"; exit 1; }
[ -f routes/admin.php ] && php -l routes/admin.php || true

echo "==> Re-run framework maintenance..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Re-run migrations to confirm all good..."
php artisan migrate --force

echo "==> Done. If anything looks off, you can restore from routes/web.php.$TS.bak"
