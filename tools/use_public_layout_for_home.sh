#!/usr/bin/env bash
set -euo pipefail
F="app/Http/Controllers/HomeController.php"
STAMP="$(date +%F_%H%M%S)"
cp -a "$F" "$F.bak_$STAMP"

# 1) If the helper line isn't present, insert it right before the return view('home', ...)
grep -q "view()->exists('public.home')" "$F" || \
  sed -i -E "/return[[:space:]]+view\('home'/i \        \$homeView = view()->exists('public.home') ? 'public.home' : 'home';" "$F"

# 2) Replace view('home' with view($homeView) in that return statement
sed -i -E "0,/return[[:space:]]+view\('home'/s//return        view(\$homeView/" "$F"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "Patched HomeController. Backup: $F.bak_$STAMP"
