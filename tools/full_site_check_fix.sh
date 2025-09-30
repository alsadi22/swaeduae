#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -Eeuo pipefail
cd /var/www/swaeduae
TS="$(date +%F_%H%M%S)"
echo "=== FULL SITE CHECK+FIX @ $(date -Is) ==="

# --- 0) Create the correct inline auth menu partial (idempotent) ---
mkdir -p resources/views/partials
cat > resources/views/partials/auth_menu.blade.php <<'BLADE'
@auth
  <li class="nav-item"><a class="nav-link" href="{{ route('my.profile') }}">My Profile</a></li>
  <li class="nav-item">
    <form method="POST" action="{{ route('logout.perform') }}" class="d-inline">
      @csrf
      <button type="submit" class="nav-link btn btn-link p-0 m-0 align-baseline">Logout</button>
    </form>
  </li>
@else
  <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
@endauth
BLADE

# --- 1) Fix bad literal include strings & misspelling everywhere ---
find resources/views -type f -name '*.blade.php' -print0 | xargs -0 sed -i -E \
 's/\("partials\.auth_menu"\)/@include("partials.auth_menu")/g;
  s/\('\''partials\.auth_menu'\''\)/@include('\''partials.auth_menu'\'')/g;
  s/\("partials\.auh_menu"\)/@include("partials.auth_menu")/g;
  s/\('\''partials\.auh_menu'\''\)/@include('\''partials.auth_menu'\'')/g' || true

# --- 2) De-duplicate mistaken repeated includes like @include@include... ---
find resources/views -type f -name '*.blade.php' -print0 | \
xargs -0 perl -0777 -i -pe 's/\@include\s*\@include\s*\@include\s*\(\s*([\'"])\s*partials\.auth_menu\s*\1\s*\)/@include($1partials.auth_menu$1)/g; s/\@include\s*\@include\s*\(\s*([\'"])\s*partials\.auth_menu\s*\1\s*\)/@include($1partials.auth_menu$1)/g; s/\@include\s*\@include\s*/@include/g;' || true

# --- 3) Ensure the auth menu is actually present in the public navbar(s) ---
CANDIDATES=(
  resources/views/argon_front/_navbar.blade.php
  resources/views/partials/header-public.blade.php
  resources/views/partials/navbar.blade.php
  resources/views/components/header.blade.php
  resources/views/public/layout.blade.php
  resources/views/layouts/public.blade.php
)
for F in "${CANDIDATES[@]}"; do
  [ -f "$F" ] || continue
  cp -a "$F" "$F.bak.$TS"
  # Remove any public "Admin Login"
  sed -i -E '/Admin Login/d' "$F"
  # If include missing, inject inside first navbar UL
  if ! grep -q "@include('partials.auth_menu')" "$F"; then
    perl -0777 -i -pe 's/(class="navbar-nav[^>]*>)(.*?)(<\/ul>)/$1$2  @include("partials.auth_menu")\n$3/s' "$F"
  fi
done

# --- 4) Convert any GET /logout anchors to POST form (site-wide) ---
grep -RIl --include='*.blade.php' -E 'href=["'\'']/logout([ "'\''>])' resources/views | while read -r f; do
  cp -a "$f" "$f.bak.$TS"
  perl -0777 -i -pe 's#<a[^>]*href=["\']/logout["\'][^>]*>(.*?)</a>#<form method="POST" action="{{ route(\'logout.perform\') }}" style="display:inline;"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="nav-link btn btn-link p-0 m-0">$1</button></form>#igs' "$f"
done

# --- 5) Clear caches, re-cache routes ---
php artisan view:clear >/dev/null || true
php artisan route:clear >/dev/null || true
php artisan route:cache >/dev/null || true

# --- 6) Deep sanity: routes, controllers, views, CSRF, HTTP probes, logs ---
echo "-- env/app --"
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo 'env=' . config('app.env') . ' php=' . PHP_VERSION . ' laravel=' . app()->version() . ' APP_DEBUG=' . (config('app.debug') ? 'true':'false') . PHP_EOL;"

echo "-- key routes --"
php artisan route:list | grep -E ' login$| register$| password\.reset| password\.store| my/profile$| my\.profile\.update| logout\.perform|generated::ALIAS_' || true

echo "-- navbar includes (should show files/lines) --"
grep -RIn --include='*.blade.php' "@include('partials.auth_menu')" resources/views || true

echo "-- GET /logout anchors (should be empty) --"
grep -RIn --include='*.blade.php' -E 'href=["'\'']/logout([ "'\''>])' resources/views || echo "OK: none found"

echo "-- homepage auth text (one of Login/Register/My Profile/Logout) --"
curl -s https://swaeduae.ae | tr -d '\n' | grep -oE 'My Profile|Logout|Login|Register' | head -n1 || echo "(no match)"

echo "-- CSRF on forms --"
for u in /login /register /forgot-password; do
  if curl -s "https://swaeduae.ae$u" | grep -q 'name="_token"'; then
    echo " $u: CSRF OK"
  else
    echo " $u: CSRF MISSING"
  fi
done

echo "-- HTTP probes --"
for u in / /login /register /forgot-password /reset-password/TEST?email=test@example.com /my/profile; do
  printf " https://swaeduae.ae%-30s -> %s\n" "$u" "$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u")"
done

echo "-- last 40 error log lines --"
tail -n 40 storage/logs/laravel-$(date +%F).log 2>/dev/null || true

echo "=== DONE ==="
