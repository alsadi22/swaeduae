#!/usr/bin/env bash
# Safe fix script: continues on errors, logs to /tmp, never closes your shell.
set -u
set -o pipefail

TS=$(date +%F_%H%M%S)
LOG="/tmp/fix_safe_${TS}.log"
exec > >(tee -a "$LOG") 2>&1

ok(){ echo "[OK] $*"; }
warn(){ echo "[WARN] $*"; }
step(){ echo; echo "== $* =="; }

step "Backups"
for f in routes/web.php resources/views/admin/_nav.blade.php resources/views/admin/_topbar.blade.php \
         resources/views/partials/account_menu.blade.php resources/views/partials/auth_menu.blade.php \
         resources/views/partials/auth_dropdown.blade.php ; do
  [ -f "$f" ] && cp -a "$f" "$f.$TS.bak" || true
done

step "PHP memory_limit -> 512M (CLI+FPM if present)"
for ini in /etc/php/8.3/cli/php.ini /etc/php/8.3/fpm/php.ini; do
  [ -f "$ini" ] && sudo sed -ri 's/^\s*memory_limit\s*=.*/memory_limit = 512M/' "$ini" || true
done
sudo systemctl reload php8.3-fpm || true

step "Normalize .env for production (no exit on error)"
grep -q '^APP_ENV=' .env && sudo sed -ri 's/^APP_ENV=.*/APP_ENV=production/' .env || echo 'APP_ENV=production' | sudo tee -a .env >/dev/null
grep -q '^APP_DEBUG=' .env && sudo sed -ri 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env || echo 'APP_DEBUG=false' | sudo tee -a .env >/dev/null

step "Require routes/partials/disable_internal.php"
grep -q "partials/disable_internal.php" routes/web.php || \
  (awk 'NR==1{print; print "require __DIR__\"/partials/disable_internal.php\";"; next}1' routes/web.php > routes/web.php.new && mv routes/web.php.new routes/web.php) || true
[ -f routes/partials/disable_internal.php ] || { mkdir -p routes/partials && echo "<?php // internal route guards" > routes/partials/disable_internal.php; } || true

step "Standardize logout to route('logout')"
grep -RIl --exclude-dir='routes/_archive' --exclude='*.bak*' --exclude='*.bak' -E "logout\.perform" resources routes \
  | xargs -r sed -i -E "s/route\((\"|')logout\.perform\1\)/route('logout')/g" || true
sed -i -E "/name\('logout\.perform'\)/d" routes/web.php || true
grep -qE "->name\('logout'\)" routes/web.php || \
  sed -i "/^<\?php/a use Illuminate\\\Support\\\Facades\\\Auth; use Illuminate\\\Http\\\Request; Route::post('\/logout', function (Request \$r){ Auth::guard()->logout(); \$r->session()->invalidate(); \$r->session()->regenerateToken(); return redirect('\/'); })->middleware('web')->name('logout');" routes/web.php || true

step "Create PWA icons if missing"
mkdir -p public/img/icons
if [ ! -f public/img/icons/icon-512x512.png ]; then
  if command -v convert >/dev/null 2>&1; then
    convert -size 512x512 canvas:white -gravity center -pointsize 52 -annotate 0 'SwaedUAE' public/img/icons/icon-512x512.png || true
    cp -a public/img/icons/icon-512x512.png public/img/icons/icon-192x192.png || true
  else
    for p in public/logo.png public/img/logo.png public/favicon.png; do
      [ -f "$p" ] && { cp -a "$p" public/img/icons/icon-512x512.png; cp -a "$p" public/img/icons/icon-192x192.png; break; }
    done
  fi
fi

step "Clear & warm caches"
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

step "Re-run audit (accepts redirects)"
BASE_URL=${BASE_URL:-https://swaeduae.ae} ADMIN_URL=${ADMIN_URL:-https://admin.swaeduae.ae} sudo -E bash tools/full_audit_v3.sh || true

echo; ok "Done. Full log: $LOG"
