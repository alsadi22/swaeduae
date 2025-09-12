#!/usr/bin/env bash
set -Eeuo pipefail
cd /var/www/swaeduae
TS="$(date +%F_%H%M%S)"

# --- tiny perl filters (robust, no shell quoting pain) ---
cat > /tmp/normalize_includes.pl <<'PL'
use strict;use warnings;local $/=undef;my $c=<>;

# Fix literal/misspelled includes -> real Blade include
$c =~ s/\(\s*["']\s*partials\.auth_menu\s*["']\s*\)/@include('partials.auth_menu')/g;
$c =~ s/\(\s*["']\s*partials\.auh_menu\s*["']\s*\)/@include('partials.auth_menu')/g;

# Collapse @include@include... -> single include
$c =~ s/\@include(?:\s*\@include)+\s*\(\s*(['"])partials\.auth_menu\1\s*\)/@include($1partials.auth_menu$1)/g;
$c =~ s/\@include\s*\@include\s*/@include /g;

print $c;
PL

cat > /tmp/fix_logout.pl <<'PL'
use strict;use warnings;local $/=undef;my $c=<>;

$c =~ s#<a[^>]*href=["']/logout["'][^>]*>(.*?)</a>#<form method="POST" action="{{ route('logout.perform') }}" style="display:inline;"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="nav-link btn btn-link p-0 m-0">$1</button></form>#igs;

print $c;
PL

cat > /tmp/ensure_csrf.pl <<'PL'
use strict;use warnings;local $/=undef;my $c=<>; 
$c =~ s/(<form\b[^>]*>)/$1\n    \@csrf/s unless $c =~ /\@csrf/;
print $c;
PL

# --- ensure the partial exists (simple, no JS required) ---
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

# --- back up likely header files once (safe) ---
for f in resources/views/argon_front/_navbar.blade.php \
         resources/views/partials/header-public.blade.php \
         resources/views/partials/navbar.blade.php \
         resources/views/components/header.blade.php; do
  [ -f "$f" ] && cp -a "$f" "$f.bak.$TS"
done

# --- normalize all blades, fix logout anchors ---
find resources/views -type f -name '*.blade.php' -print0 | while IFS= read -r -d '' f; do
  perl /tmp/normalize_includes.pl "$f" > "$f.$TS.tmp" && mv "$f.$TS.tmp" "$f"
  perl /tmp/fix_logout.pl         "$f" > "$f.$TS.tmp" && mv "$f.$TS.tmp" "$f"
done

# --- remove any public "Admin Login" link if present ---
grep -RIl --include='*.blade.php' -E 'Admin Login' resources/views | xargs -r -I{} sed -i -E '/Admin Login/d' {}

# --- ensure @csrf on login form (if exists) ---
if [ -f resources/views/auth/login.blade.php ]; then
  cp -a resources/views/auth/login.blade.php resources/views/auth/login.blade.php.bak.$TS
  perl /tmp/ensure_csrf.pl resources/views/auth/login.blade.php > resources/views/auth/login.blade.php.$TS.tmp && mv resources/views/auth/login.blade.php.$TS.tmp resources/views/auth/login.blade.php
fi

# --- clear & recache ---
php artisan view:clear
php artisan route:clear && php artisan route:cache

# --- quick audit ---
echo "=== AUDIT @ $(date -Is) ==="
echo "-- BAD literal/misspelled includes (should be none):"
grep -RIn --include='*.blade.php' -E '\("partials\.(auth|auh)_menu"\)|\('"'"'partials\.(auth|auh)_menu'"'"'\)' resources/views || echo "none"
echo "-- duplicate @include sequences (should be none):"
grep -RIn --include='*.blade.php' '@include@include' resources/views || echo "none"
echo "-- navbar includes present (file:line):"
grep -RIn --include='*.blade.php' "@include('partials.auth_menu')" resources/views || true
echo "-- / and /login status:"
for u in / /login; do echo -n " https://swaeduae.ae$u -> "; curl -s -o /dev/null -w "%{http_code}\n" "https://swaeduae.ae$u"; done
echo "-- /login CSRF:"
curl -s "https://swaeduae.ae/login" | grep -q 'name="_token"' && echo "OK" || echo "MISSING"
