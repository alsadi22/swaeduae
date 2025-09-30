#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
F="app/Http/Controllers/HomeController.php"
STAMP="$(date +%F_%H%M%S)"
[ -f resources/views/public/home.blade.php ] || { echo "❌ resources/views/public/home.blade.php missing"; exit 1; }
cp -a "$F" "$F.bak_$STAMP"

# Replace the return line to always use public.home
# (handles either view('home'...) or view($homeView...))
sed -i -E "s@return[[:space:]]+view\([^)]*@return view('public.home',[@" "$F"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Forced public.home. Backup: $F.bak_$STAMP"
