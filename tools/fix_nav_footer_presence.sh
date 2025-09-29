#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

echo "== A) Verify home view extends layouts.public =="
for V in resources/views/home.blade.php resources/views/public/home.blade.php; do
  [ -f "$V" ] || continue
  echo "  checking $V"
  head -n1 "$V" | grep -q "@extends('layouts.public')" || {
    echo "  -> $V does NOT extend layouts.public; fixing"
    cp -a "$V" "$V.bak_$STAMP"
    awk 'NR==1{print "@extends('\''layouts.public'\'')"; next} {print}' "$V" > "$V.__tmp__" && mv "$V.__tmp__" "$V"
  }
done

echo "== B) Ensure partials exist =="
[ -f resources/views/partials/navbar.blade.php ] || { echo "❌ missing resources/views/partials/navbar.blade.php"; exit 2; }
[ -f resources/views/partials/footer.blade.php ] || { echo "❌ missing resources/views/partials/footer.blade.php"; exit 2; }

echo "== C) Ensure public layout includes partials with markers =="
LAY="resources/views/layouts/public.blade.php"
[ -f "$LAY" ] || { echo "❌ $LAY missing"; exit 3; }
cp -a "$LAY" "$LAY.bak_$STAMP"

# Insert markers around includes (idempotent)
grep -q "pub-navbar:start" "$LAY" || sed -i "s/@includeIf('partials.navbar')/<!-- pub-navbar:start -->@includeIf('partials.navbar')<!-- pub-navbar:end -->/" "$LAY"
grep -q "pub-footer:start" "$LAY" || sed -i "s/@includeIf('partials.footer')/<!-- pub-footer:start -->@includeIf('partials.footer')<!-- pub-footer:end -->/" "$LAY"

echo "== D) Rebuild caches =="
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
php artisan route:clear >/dev/null || true
php artisan route:cache >/dev/null || true

echo "== E) Live checks =="
HTML="$(curl -sk https://swaeduae.ae/ || true)"
if ! grep -q "<!-- pub-navbar:start -->" <<<"$HTML"; then
  echo "❌ Navbar include marker NOT found in live HTML. The home page may not be using layouts.public. Aborting."
  echo "Hint: check which route serves '/' and confirm it returns the 'home' view."
  exit 4
fi
if ! grep -q "<!-- pub-footer:start -->" <<<"$HTML"; then
  echo "❌ Footer include marker NOT found in live HTML. Aborting."
  exit 5
fi

echo "✅ Found navbar/footer markers in live HTML."
# Also show the actual tags presence:
printf "Home has <nav>?   "; grep -m1 -o '<nav[^>]*>' <<<"$HTML" || echo "MISSING"
printf "Home has <footer>? "; grep -m1 -o '<footer[^>]*>' <<<"$HTML" || echo "MISSING"
