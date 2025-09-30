PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
# SwaedUAE – Auth Menu Audit (no writes, no restarts)
set -u; set -o pipefail
BASE_URL="${BASE_URL:-https://swaeduae.ae}"
ADMIN_URL="${ADMIN_URL:-https://admin.swaeduae.ae}"
APP_ROOT="${APP_ROOT:-/var/www/swaeduae/current}"
TS="$(date +%F_%H%M%S)"
OUT="$APP_ROOT/public/health/auth_menu_audit_${TS}.txt"
mkdir -p "$APP_ROOT/public/health"

# helpers
pass(){ echo "[PASS] $*" | tee -a "$OUT"; }
warn(){ echo "[WARN] $*" | tee -a "$OUT"; }
fail(){ echo "[FAIL] $*" | tee -a "$OUT"; }

{
echo "=== AUTH MENU AUDIT @ $TS ==="
echo "BASE_URL=$BASE_URL | ADMIN_URL=$ADMIN_URL"
echo "APP_ROOT=$APP_ROOT"
echo

echo "--- 1) HTTP checks (status + CSRF) ---"
code(){ curl -s -o /dev/null -w "%{http_code}" "$1"; }
printf "/           -> %s\n"   "$(code "$BASE_URL/")"
printf "/login      -> %s\n"   "$(code "$BASE_URL/login")"
printf "/register   -> %s\n"   "$(code "$BASE_URL/register")"
printf "/org/login  -> %s\n"   "$(code "$BASE_URL/org/login")"
# admin may redirect; we also test canonical admin host
printf "/admin/login-> %s\n"   "$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/admin/login")"
printf "admin host  -> %s\n"   "$(curl -s -o /dev/null -w "%{http_code}" "$ADMIN_URL/login")"
curl -sL "$BASE_URL/login" | grep -q 'name="_token"' && pass "Volunteer login CSRF present" || warn "Volunteer login CSRF missing"
curl -sL "$ADMIN_URL/login" | grep -q 'name=\"_token\"' && pass "Admin login CSRF present" || warn "Admin login CSRF missing"

echo
echo "--- 2) Home page content (Sign In / menu markers) ---"
TMP="$(mktemp)"; curl -sL "$BASE_URL/" > "$TMP"
grep -qE "Volunteer Sign In|Sign In" "$TMP" && pass "Home shows a Sign In label" || warn "No 'Sign In' text on home"
grep -q "dropdown-menu" "$TMP" && pass "dropdown-menu markup present" || warn "No 'dropdown-menu' in home HTML"
grep -q 'data-bs-toggle="dropdown"' "$TMP" && pass "Bootstrap dropdown toggler attribute present" || warn "No 'data-bs-toggle=\"dropdown\"' in home HTML"
grep -qE "/login|/register|/org/login" "$TMP" && pass "Auth links detected in home HTML" || warn "No /login,/register,/org/login links in home HTML"
rm -f "$TMP"

echo
echo "--- 3) Blade partials presence ---"
[ -f "$APP_ROOT/resources/views/partials/auth_menu.blade.php" ] && pass "partials/auth_menu.blade.php exists" || warn "partials/auth_menu.blade.php missing"
[ -f "$APP_ROOT/resources/views/partials/account_menu.blade.php" ] && pass "partials/account_menu.blade.php exists" || warn "partials/account_menu.blade.php missing"
# Quick lint of partials (look for key anchors/toggler)
if [ -f "$APP_ROOT/resources/views/partials/auth_menu.blade.php" ]; then
  grep -q 'data-bs-toggle="dropdown"' "$APP_ROOT/resources/views/partials/auth_menu.blade.php" && pass "auth_menu has dropdown toggler" || warn "auth_menu missing dropdown toggler"
  grep -qE "href=\"\{\{\s*url\('/login'\)\s*\}\}\"" "$APP_ROOT/resources/views/partials/auth_menu.blade.php" && pass "auth_menu has /login" || warn "auth_menu missing /login"
  grep -qE "href=\"\{\{\s*url\('/register'\)\s*\}\}\"" "$APP_ROOT/resources/views/partials/auth_menu.blade.php" && pass "auth_menu has /register" || warn "auth_menu missing /register"
  grep -qE "href=\"\{\{\s*url\('/org/login'\)\s*\}\}\"" "$APP_ROOT/resources/views/partials/auth_menu.blade.php" && pass "auth_menu has /org/login" || warn "auth_menu missing /org/login"
fi

echo
echo "--- 4) Header includes (is the menu inserted into navbar?) ---"
# find likely header files
mapfile -t HEADERS < <(grep -RIl --include='*header*.blade.php' -E "navbar-nav" "$APP_ROOT/resources/views" 2>/dev/null || true)
if [ "${#HEADERS[@]}" -eq 0 ]; then
  warn "No header Blade with 'navbar-nav' found"; 
else
  echo "Header candidates:"; for h in "${HEADERS[@]}"; do echo "  - ${h#$APP_ROOT/}"; done
  # Do any headers include our partials?
  HIT_INC=0
  for h in "${HEADERS[@]}"; do
    if grep -qE "@includeIf\('partials\.(auth_menu|account_menu)'\)|@include\('partials\.(auth_menu|account_menu)'\)" "$h"; then
      echo "  * includes found in: ${h#$APP_ROOT/}"
      HIT_INC=1
    fi
  done
  [ "$HIT_INC" -eq 1 ] && pass "Header includes auth/account menu partials" || warn "Header does NOT include auth/account menu partials"
fi

echo
echo "--- 5) Layout JS needed for dropdowns ---"
# We only warn if NO bootstrap bundle reference is found anywhere in public layout/views
JS_HIT=$(grep -RIn --include='*.blade.php' -E "bootstrap(\.bundle)?\.min\.js|data-bs-toggle" "$APP_ROOT/resources/views" | wc -l | tr -d ' ')
[ "$JS_HIT" -gt 0 ] && pass "Bootstrap JS markers found in views" || warn "No Bootstrap JS markers found (dropdowns may not open on click)"

echo
echo "--- 6) Route sanity (login/register available to guests) ---"
if php -v >/dev/null 2>&1; then
  if php artisan route:list >/dev/null 2>&1; then
    php artisan route:list | grep -qE "\slogin\s" && pass "Route 'login' registered" || warn "No 'login' route registered"
    php artisan route:list | grep -qE "\sregister\s" && pass "Route 'register' registered" || warn "No 'register' route registered"
    php artisan route:list | grep -qi "org/login" && pass "Org login route present" || warn "No '/org/login' route"
  else
    warn "artisan route:list failed"
  fi
else
  warn "PHP/Artisan not available for route checks"
fi

echo
echo "--- DONE ---"
} | tee "$OUT"

echo; echo "Report: $OUT"
