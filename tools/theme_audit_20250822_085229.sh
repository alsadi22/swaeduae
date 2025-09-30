PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
export LANG=C

BASE="${BASE:-https://swaeduae.ae}"

hr(){ printf '\n==== %s ====\n' "$*"; }
mini(){ printf '%s\n' "-- $* --"; }

hr "0) Versions"
php -v | head -n1 || true
php artisan --version || true

hr "1) Local view scan — where themes/footers are used"
mini "Argon admin assets referenced in views (should be admin-only)"
grep -RInE 'argon-dashboard|nucleo-|perfect-scrollbar' resources/views || true

mini "Public master layout(s) that include Argon or multiple footers"
nl -ba resources/views/layouts/app.blade.php | sed -n '1,240p' || true
grep -nE '@include\((["'\''])argon_front[/_]+_footer\1\)' resources/views/layouts/app.blade.php || true
grep -nE '@include(?:If)?\((["'\''])components\.footer\1\)' resources/views/layouts/app.blade.php || true
grep -nE '<main\b' resources/views/layouts/app.blade.php || true

mini "Any raw <footer> tags outside the canonical component"
grep -RIn '<footer\b' resources/views | grep -v 'resources/views/components/footer.blade.php' || true

mini "All footer-like partials/components that might collide"
find resources/views -type f -name '*footer*.blade.php' -print 2>/dev/null || true

hr "2) Live pages — asset leaks, duplicate footer/main, lang headers"
paths=("/" "/about" "/events" "/opportunities" "/gallery" "/verify" "/contact")
for p in "${paths[@]}"; do
  echo
  mini "GET ${BASE}${p} [Accept-Language: en]"
  curl -sS -D - -o /dev/null -H 'Accept-Language: en' "${BASE}${p}" \
    | egrep -i 'HTTP/|Content-Type|Content-Language|Vary' || true

  HTML="$(curl -sS -H 'Accept-Language: en' "${BASE}${p}")"
  printf 'footer tags: '; printf '%s' "$HTML" | tr -d '\n' | grep -o '<footer' | wc -l
  printf 'main tags:   '; printf '%s' "$HTML" | tr -d '\n' | grep -o '<main' | wc -l
  printf 'Argon assets present? '; printf '%s' "$HTML" | grep -E 'argon-dashboard|min\.css|nucleo-icons|perfect-scrollbar' | wc -l
done

hr "3) Locale behavior — does en vs ar change Content-Language?"
for lang in en ar; do
  mini "Accept-Language: $lang on /"
  curl -sS -D - -o /dev/null -H "Accept-Language: $lang" "${BASE}/" \
    | egrep -i 'HTTP/|Content-Language|Vary' || true
done

hr "4) Routes snapshot (to understand layouts split admin/public)"
php artisan route:list | egrep -i 'GET|admin|login|events|opportunit|gallery|verify|about|contact' || true

hr "Done (read-only audit complete)"
