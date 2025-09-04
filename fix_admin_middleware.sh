#!/bin/bash
cd /home3/vminingc/swaeduae.ae/laravel-app

# Regenerate Composer autoload files
composer dump-autoload

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Caches cleared and autoload regenerated. Try accessing /admin again."
