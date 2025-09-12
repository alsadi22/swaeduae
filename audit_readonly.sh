#!/bin/bash
set -euo pipefail
APP="/home3/vminingc/swaeduae.ae/laravel-app"
PHP_BIN="${PHP:-/opt/alt/php84/usr/bin/php}"
BASE="${BASE:-https://swaeduae.ae}"
cd "$APP"

TS="$(date +%Y%m%d-%H%M%S)"
OUT="storage/logs/audit_${TS}"
mkdir -p "$OUT"

http_code(){ curl -sS -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || true; }
http_loc(){ curl -sSI "$1" 2>/dev/null | awk -F': ' 'tolower($1)=="location"{print $2}' | tr -d '\r'; }

# 00) Env
{ echo "PWD: $(pwd)"; "$PHP_BIN" -v; } > "$OUT/00_env.txt" 2>&1

# 01) HTTP smoke (unauth) with Location (helps see 302 targets)
{
  printf "%-24s %-6s %s\n" "PATH" "CODE" "LOCATION(if 3xx)"
  while read -r p; do
    [ -z "$p" ] && continue
    code="$(http_code "$BASE$p")"
    loc=""
    case "$code" in
      3*) loc="$(http_loc "$BASE$p")" ;;
    esac
    printf "%-24s %-6s %s\n" "$p" "$code" "$loc"
  done <<EOF
/
 /login
 /org/login
 /org/register
 /org
 /org/dashboard
 /profile
 /volunteer/profile
 /volunteer/dashboard
 /admin
 /admin/login
 /my/dashboard
EOF
} | tee "$OUT/01_http_smoke.txt" >/dev/null

# 02) Full routes table (no --columns; compatible output)
"$PHP_BIN" artisan route:list > "$OUT/02_routes.txt" 2>&1

# 03) Auth-focused subset (login/logout/register/profile/dashboard/org/admin)
grep -E '(^|\s)(GET|POST|PUT|PATCH|DELETE)\s' "$OUT/02_routes.txt" \
 | grep -Ei '(^|/)(org|admin)(/|$)|login|logout|register|profile|dashboard' \
 | tee "$OUT/03_auth_routes.txt" >/dev/null || true

# Helpers to trim cells from the route table split by '|'
trim(){ sed 's/^[[:space:]]*//; s/[[:space:]]*$//'; }

# 04) Find POST handlers for /login and /org/login by parsing the table
POST_VOL="$("$PHP_BIN" artisan route:list | \
  awk -F'\\|' '/\|POST/ && $3 ~ /^[[:space:]]*login[[:space:]]*$/ {gsub(/^ *| *$/,"",$5); print $5; exit}')"
POST_ORG="$("$PHP_BIN" artisan route:list | \
  awk -F'\\|' '/\|POST/ && $3 ~ /^[[:space:]]*org\/login[[:space:]]*$/ {gsub(/^ *| *$/,"",$5); print $5; exit}')"

{
  echo "POST /login      -> ${POST_VOL:-not-found}"
  echo "POST /org/login  -> ${POST_ORG:-not-found}"
} | tee "$OUT/04_login_handlers.txt" >/dev/null

# 05) Dump controller sources for those handlers and grep for redirect targets
dump_controller () {
  local action="$1"
  [ -z "$action" ] && return 0
  local cls="${action%@*}"
  local file="app/$(echo "$cls" | sed -E 's#^App\\\\##; s#\\\\#/#g').php"
  {
    echo "--- $action => $file"
    if [ -f "$file" ]; then
      nl -ba "$file" | sed -n '1,240p'
      echo ">>> redirect/intended/HOME/profile/org/dashboard grep:"
      grep -nE "redirect\(|intended\(|RouteServiceProvider::HOME|/profile|/org/dashboard" "$file" || true
      echo
    else
      echo "!! MISSING $file"
    fi
  } >> "$OUT/05_controller_sources.txt"
}
: > "$OUT/05_controller_sources.txt"
dump_controller "$POST_VOL"
dump_controller "$POST_ORG"

# 06) RouteServiceProvider::HOME (post-login default)
{
  echo "== RouteServiceProvider::HOME =="
  grep -n "const HOME" app/Providers/RouteServiceProvider.php || true
  nl -ba app/Providers/RouteServiceProvider.php | sed -n '1,160p' || true
} > "$OUT/06_home_const.txt"

# 07) RedirectIfAuthenticated middleware
RIA_FILE="$(grep -RIl --include='*.php' 'class RedirectIfAuthenticated' app/Http/Middleware || true)"
{
  echo "File: ${RIA_FILE:-not-found}"
  [ -n "$RIA_FILE" ] && nl -ba "$RIA_FILE" | sed -n '1,240p'
} > "$OUT/07_redirect_if_auth.txt" 2>/dev/null

# 08) Grep controllers for redirect targets that could force /profile
grep -RIn --include='*.php' -E "redirect\(|intended\(|/profile|/org/dashboard|RouteServiceProvider::HOME" app/Http/Controllers \
  > "$OUT/08_redirect_grep.txt" || true

# 09) Profile routes only (GET/HEAD)
grep -E '(^|\s)(GET|HEAD)\s' "$OUT/02_routes.txt" | grep -E '(^|/| )profile( |/|$)' \
  > "$OUT/09_profile_route.txt" || true

# 10) auth.php snapshot
nl -ba config/auth.php | sed -n '1,260p' > "$OUT/10_auth_config.txt" || true

# 11) Org controllers inventory and any login/auth methods
{
  ls -l app/Http/Controllers/Org 2>/dev/null || true
  grep -RIn --include='*.php' -E "class\s+.*Auth|function\s+(login|authenticate|store)\s*\(" app/Http/Controllers/Org 2>/dev/null || true
} > "$OUT/11_org_auth_controllers.txt"

# 12) Risky DB usage in blades (should be minimal)
grep -RIn --include='*.blade.php' -E 'DB::|->get\(|->first\(|->count\(|->paginate\(' resources 2>/dev/null \
  > "$OUT/12_blade_db_uses.txt" || echo "none" > "$OUT/12_blade_db_uses.txt"

# 13) Hard-coded redirect targets across app code
grep -RIn --include='*.php' -E "['\"]/profile['\"]|['\"]/org/dashboard['\"]" app 2>/dev/null \
  > "$OUT/13_hardcoded_targets.txt" || true

# 14) Latest log tail
LOG="$(ls -t storage/logs/laravel*.log 2>/dev/null | head -n1 || true)"
{
  echo "Log: ${LOG:-none}"
  [ -n "$LOG" ] && tail -n 200 "$LOG" || true
} > "$OUT/14_log_tail.txt"

echo "AUDIT COMPLETE → $OUT"
ls -1 "$OUT"
