#!/bin/bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
PHP_BIN="${PHP:-/opt/alt/php84/usr/bin/php}"
cd "$APP"

echo "[1/5] Ensure /org -> /org/dashboard redirect exists"
WEB="routes/web.php"
if ! grep -q "Route::redirect('/org', '/org/dashboard'" "$WEB" 2>/dev/null; then
  cp -a "$WEB" "${WEB}.bak_$(date +%Y%m%d%H%M%S)"
  printf "\n// SwaedUAE: ensure /org lands on org dashboard\nRoute::redirect('/org', '/org/dashboard', 302)->name('org.home');\n" >> "$WEB"
  echo "  + added to $WEB"
else
  echo "  = already present"
fi

echo "[2/5] Make RedirectIfAuthenticated role-aware"
RIA="$(grep -RIl --include='*.php' 'class RedirectIfAuthenticated' app/Http/Middleware || true)"
if [ -n "$RIA" ]; then
  cp -a "$RIA" "${RIA}.bak_$(date +%Y%m%d%H%M%S)"
  # import Auth if missing
  grep -q 'use Illuminate\\Support\\Facades\\Auth;' "$RIA" || \
    perl -0777 -i -pe 's/(namespace\s+[^\n;]+;\s*\R)/$1use Illuminate\\Support\\Facades\\Auth;\n/s' "$RIA"
  # add helper if missing
  if ! grep -q 'function roleHome' "$RIA"; then
    perl -0777 -i -pe 's/(class\s+RedirectIfAuthenticated[^{]*\{)/$1\n    private function roleHome(): string {\n        $u = Auth::user();\n        if ($u && method_exists($u, "hasRole") && $u->hasRole("org")) {\n            return "\/org\/dashboard";\n        }\n        return "\/profile";\n    }\n/s' "$RIA"
  fi
  # replace HOME with roleHome()
  perl -0777 -i -pe 's/RouteServiceProvider::HOME/\$this->roleHome()/g' "$RIA"
  # upgrade redirect(...) to intended(...)
  perl -0777 -i -pe 's/return\s+redirect\((?:\s*\$this->roleHome\(\)\s*)\);/return redirect()->intended($this->roleHome());/g' "$RIA"
  $PHP_BIN -l "$RIA" >/dev/null
  echo "  * patched: $RIA"
else
  echo "  ! WARN: RedirectIfAuthenticated not found"
fi

echo "[3/5] Force post-login fallbacks"
VOL="$($PHP_BIN artisan route:list | awk '/POST/ && $2=="login"{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
ORG="$($PHP_BIN artisan route:list | awk '/POST/ && $2 ~ /\/org\/login/{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
patch_fallback () {
  local action="$1" ; local target="$2"
  [ -z "$action" ] && return 0
  local cls="${action%@*}" ; local file="app/$(echo "$cls" | sed -E 's#^App\\\\##; s#\\\\#/#g').php"
  [ -f "$file" ] || { echo "  ! missing $file"; return 0; }
  cp -a "$file" "${file}.bak_$(date +%Y%m%d%H%M%S)"
  # intended() → intended($target)
  perl -0777 -i -pe "s/redirect\\(\\)->intended\\([^)]*\\)/redirect()->intended('$target')/g" "$file"
  # explicit redirect(...) or to_route(...) → redirect('$target')
  perl -0777 -i -pe "s/return\\s+(?:to_route\\([^)]*\\)|redirect\\([^)]*\\))\\s*;\\s*$/return redirect('$target');/mg" "$file"
  # HOME constant → literal
  perl -0777 -i -pe "s/RouteServiceProvider::HOME/'$target'/g" "$file"
  $PHP_BIN -l "$file" >/dev/null
  echo "  * patched: $file  (→ $target)"
}
[ -n "$VOL" ] && patch_fallback "$VOL" "/profile" || echo "  = POST /login handler not found"
[ -n "$ORG" ] && patch_fallback "$ORG" "/org/dashboard" || echo "  = POST /org/login handler not found"

echo "[4/5] Clear caches"
$PHP_BIN artisan optimize:clear >/dev/null
$PHP_BIN artisan route:cache     >/dev/null
$PHP_BIN artisan view:clear      >/dev/null
echo "  caches cleared"

echo "[5/5] Quick unauth smoke"
BASE="${BASE:-https://swaeduae.ae}"
printf "%-20s %s\n" "/login"         "$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/login")"
printf "%-20s %s\n" "/org/login"     "$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/org/login")"
printf "%-20s %s\n" "/org/dashboard" "$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/org/dashboard")"
printf "%-20s %s\n" "/profile"       "$(curl -sS -o /dev/null -w '%{http_code}' "$BASE/profile")"

echo "FIX COMPLETE."
