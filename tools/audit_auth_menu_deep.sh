#!/usr/bin/env bash
set -u; set -o pipefail
BASE_URL="${BASE_URL:-https://swaeduae.ae}"
ADMIN_URL="${ADMIN_URL:-https://admin.swaeduae.ae}"
APP_ROOT="${APP_ROOT:-/var/www/swaeduae/current}"
OUT="$APP_ROOT/public/health/auth_menu_deep_$(date +%F_%H%M%S).txt"
mkdir -p "$APP_ROOT/public/health"

p(){ printf "%s\n" "$*" | tee -a "$OUT"; }
sep(){ p "\n==================== $* ===================="; }

{
sep "A) HTTP + LIVE HTML (home header snippet)"
code_home=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/")
p "/ -> $code_home"
# pull home HTML and show only navbar area
TMP=$(mktemp)
curl -sL "$BASE_URL/" > "$TMP"
p "-- tokens present? --"
grep -Eo 'Sign In|dropdown-menu|data-bs-toggle="dropdown"' "$TMP" | sort | uniq -c || true
p "-- navbar snippet --"
tr '\n' ' ' < "$TMP" \
  | sed 's/</\n</g' \
  | awk '/<nav/{flag=1} flag{print} /<\/nav>/{flag=0}' \
  | sed -n '1,40p'
rm -f "$TMP"

sep "B) Which route handles '/' (Action column)"
php artisan route:list --columns=Method,URI,Name,Action 2>/dev/null | awk '$2=="/"{print}' | tee -a "$OUT"

sep "C) Guess homepage view (search for hero text + extends)"
# find view containing the big homepage headline or hero
mapfile -t VIEWS < <(grep -RIl --include='*.blade.php' -E "Welcome to SwaedUAE|Find, join, and track|Featured Opportunities" resources/views 2>/dev/null)
if [ "${#VIEWS[@]}" -gt 0 ]; then
  p "Candidate view(s):"; for v in "${VIEWS[@]}"; do p "  - ${v#$APP_ROOT/}"; done
  for v in "${VIEWS[@]}"; do
    p "-- extends/layout lines in ${v#$APP_ROOT/}:"
    grep -nE "@extends|@include\('partials\.header|@include\('components\.header" "$v" || true
  done
else
  p "No obvious homepage view found (could be a controller/closure)."
fi

sep "D) Which layout includes which header"
LAYOUTS=$(grep -RIl --include='*.blade.php' -E "@include\(['\"]partials\.header['\"]\)|@include\(['\"]components\.header['\"]\)" resources/views 2>/dev/null || true)
if [ -n "$LAYOUTS" ]; then
  p "Layouts that include a header partial:"
  echo "$LAYOUTS" | sed "s#^#$APP_ROOT/#" | sed "s#$APP_ROOT/##" | sed '$!s/$/\n/' | tee -a "$OUT" >/dev/null
  while IFS= read -r L; do
    p "-- header include lines in ${L#$APP_ROOT/}:"
    grep -nE "@include\(['\"](partials|components)\.header['\"]\)" "$L" || true
  done <<< "$LAYOUTS"
else
  p "No layouts with header includes found."
fi

sep "E) Do header partials actually include auth menus?"
for H in resources/views/partials/header.blade.php resources/views/components/header.blade.php; do
  if [ -f "$H" ]; then
    p "Header: ${H#$APP_ROOT/}"
    grep -nE "@includeIf?\('partials\.(auth_menu|account_menu)'\)" "$H" || p "  (no auth/account includes found)"
    # show the ULs to ensure our block is inside a navbar UL
    p "  -- first navbar <ul> block --"
    awk '
      /<ul[^>]*navbar-nav/{inul=1; print; next}
      inul && /<\/ul>/{print; inul=0; exit}
      inul {print}
    ' "$H" || true
  fi
done

sep "F) Do the auth partials contain the expected markup?"
for P in resources/views/partials/auth_menu.blade.php resources/views/partials/account_menu.blade.php; do
  if [ -f "$P" ]; then
    p "Partial: ${P#$APP_ROOT/}"
    grep -n 'data-bs-toggle="dropdown"' "$P" || p "  (no dropdown toggler attr)"
    grep -nE "href=\"\{\{\s*url\('/(login|register|org/login)'\)\s*\}\}\"" "$P" || p "  (no /login|/register|/org/login)"
  else
    p "Missing partial: ${P#$APP_ROOT/}"
  fi
done

sep "G) Is Bootstrap JS referenced (dropdowns need it)?"
grep -RIn --include='*.blade.php' -E "bootstrap(\.bundle)?\.min\.js" resources/views | sed 's/^/  /' || p "  (no bootstrap js markers in views)"

sep "H) Final: show the <ul class='navbar-nav'> from LIVE HOME HTML"
TMP=$(mktemp)
curl -sL "$BASE_URL/" > "$TMP"
tr '\n' ' ' < "$TMP" \
  | sed 's/</\n</g' \
  | awk '/<ul[^>]*navbar-nav/{flag=1} flag{print} /<\/ul>/{if(flag){print; exit}}'
rm -f "$TMP"

} | tee "$OUT"
echo "Report: $OUT"
