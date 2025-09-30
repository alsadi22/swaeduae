PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -Eeuo pipefail
cd /var/www/swaeduae
TS="$(date +%F_%H%M%S)"

echo "== 1) Ensure inline auth menu partial =="
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

echo "== 2) Fix bad literal ('partials.auth_menu') occurrences =="
BADFILES=$(grep -RIl --include='*.blade.php' -E '\("partials\.auth_menu"\)|\('\''partials\.auth_menu'\''\)' resources/views || true)
for f in $BADFILES; do
  echo "  * fixing include in: $f"
  cp -a "$f" "$f.bak.$TS"
  # replace ("partials.auth_menu") or ('partials.auth_menu') with @include('partials.auth_menu')
  sed -i -E "s/\(\"partials\.auth_menu\"\)/@include('partials.auth_menu')/g" "$f"
  sed -i -E "s/\('partials\.auth_menu'\)/@include('partials.auth_menu')/g" "$f"
done

echo "== 3) Inject @include('partials.auth_menu') into public navbar(s) =="
# Likely header locations for the public site
CANDIDATES="
resources/views/argon_front/_navbar.blade.php
resources/views/partials/header-public.blade.php
resources/views/partials/navbar.blade.php
resources/views/components/header.blade.php
resources/views/public/layout.blade.php
resources/views/layouts/public.blade.php
"
for F in $CANDIDATES; do
  [ -f "$F" ] || continue
  echo "  * patching navbar in: $F"
  cp -a "$F" "$F.bak.$TS"
  # Remove any public "Admin Login" link
  sed -i -E '/Admin Login/d' "$F"
  # If the include isn't already there, add it before the closing </nav> (right-aligned UL)
  if ! grep -q "partials.auth_menu" "$F"; then
    sed -i '/<\/nav>/i \  <ul class="navbar-nav ms-auto">@include('"'"'partials.auth_menu'"'"')</ul>' "$F"
  fi
done

echo "== 4) Ensure NO GET /logout anchors remain (convert to POST form) =="
LOGOUTFILES=$(grep -RIl --include='*.blade.php' -E 'href=["'\'']/logout([ "'\''>])' resources/views || true)
for f in $LOGOUTFILES; do
  echo "  * fixing logout anchor in: $f"
  cp -a "$f" "$f.bak.$TS"
  perl -0777 -i -pe 's#<a[^>]*href=["\']/logout["\'][^>]*>(.*?)</a>#<form method="POST" action="{{ route(\'logout.perform\') }}" style="display:inline;"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="nav-link btn btn-link p-0 m-0">$1</button></form>#igs' "$f"
done

echo "== 5) Clear caches & recache routes =="
php artisan view:clear >/dev/null
php artisan route:clear >/dev/null
php artisan route:cache >/dev/null

echo "== 6) Sanity checks =="
echo "-- files that now include the auth menu --"
grep -RIn --include='*.blade.php' "@include('partials.auth_menu')" resources/views || true

echo "-- homepage contains one of (Login / Register / My Profile / Logout) --"
curl -s https://swaeduae.ae | tr -d '\n' | grep -oE 'My Profile|Logout|Login|Register' | head -n1 || echo "(no match)"

echo "-- homepage should NOT contain bad literal include --"
curl -s https://swaeduae.ae | tr -d '\n' | grep -o '\("partials\.auth_menu"\)|\('\''partials\.auth_menu'\''\)' || echo "(ok)"
