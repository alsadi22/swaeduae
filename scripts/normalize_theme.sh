#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup views ..."
mkdir -p backups && tar -czf "backups/views_$TS.tgz" resources/views

# 1) PUBLIC: move everyone to layouts.app (skip admin/)
find resources/views -type f -name "*.blade.php" ! -path "resources/views/admin/*" -print0 \
 | xargs -0 sed -i -E \
   -e "s/@extends\(['\"]layouts\.master['\"]\)/@extends('layouts.app')/g" \
   -e "s/@extends\(['\"]layouts\.public['\"]\)/@extends('layouts.app')/g"

# 2) ADMIN: move everyone to admin.layout
find resources/views/admin -type f -name "*.blade.php" -print0 \
 | xargs -0 sed -i -E \
   -e "s/@extends\(['\"]layouts\.admin['\"]\)/@extends('admin.layout')/g" \
   -e "s/@extends\(['\"]layouts\.admin_theme['\"]\)/@extends('admin.layout')/g"

# 3) Categories page: fix extends + Route::has + category value usage
if [ -f resources/views/categories/index.blade.php ]; then
  sed -i -E "1s/@extends\(['\"]layouts\.master['\"]\)/@extends('layouts.app')/" resources/views/categories/index.blade.php
  sed -i -E "s#\{\{\s*IlluminateSupportFacadesRoute::has\('public\.opportunities'\)\s*\?\s*route\('public\.opportunities'\)\s*:\s*url\('/opportunities'\)\s*\}\}#{{ \\Illuminate\\Support\\Facades\\Route::has('public.opportunities') ? route('public.opportunities') : url('/opportunities') }}#g" resources/views/categories/index.blade.php
  sed -i -E "s/urlencode\(\s*\$cat->name\s*\)/urlencode(\$cat)/g" resources/views/categories/index.blade.php
fi

# Common stragglers that should be public master
sed -i -E "1s/@extends\(['\"]layouts\.master['\"]\)/@extends('layouts.app')/" \
  resources/views/about.blade.php \
  resources/views/home.blade.php \
  resources/views/events/index.blade.php \
  resources/views/events/show.blade.php \
  resources/views/public/opportunities/index.blade.php \
  resources/views/public/opportunities/show.blade.php \
  resources/views/partners/index.blade.php \
  resources/views/organizations/register.blade.php \
  resources/views/auth/login.blade.php \
  resources/views/auth/register.blade.php \
  resources/views/auth/verify-email.blade.php \
  resources/views/profile/show.blade.php 2>/dev/null || true

echo "==> Clear & rebuild caches ..."
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Post-check:"
echo -n "layouts.app count: "; grep -Rno \"@extends('layouts.app')\" resources/views | wc -l
echo -n "admin.layout count: "; grep -Rno \"@extends('admin.layout')\" resources/views | wc -l
