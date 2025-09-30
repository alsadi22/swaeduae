#!/bin/bash
PHP_BIN=${PHP_BIN:-php}
# --- SwaedUAE: full site audit (routes, redirects, auth flows) — READ ONLY ---
set -euo pipefail
PHP_BIN=${PHP_BIN:-php}
APP="/home3/vminingc/swaeduae.ae/laravel-app"
PHP_BIN="${PHP:-/opt/alt/php84/usr/bin/php}"
BASE="${BASE:-https://swaeduae.ae}"
cd "$APP"

TS="$(date +%Y%m%d-%H%M%S)"
OUT="storage/logs/audit_${TS}"
mkdir -p "$OUT"

# 00) Env
{
  echo "PWD: $(pwd)"
  "$PHP_BIN" -v
} > "$OUT/00_env.txt" 2>&1

# Helper to fetch HTTP code without aborting on error
http_code () {
  local url="$1"
  local code
  code="$(curl -sS -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || true)"
  echo "${code:-ERR}"
}

# 01) HTTP smoke (unauth)
{
  echo "== QUICK HTTP SMOKE (unauth) =="
  while read -r p; do
    [ -z "$p" ] && continue
    code="$(http_code "$BASE$p")"
    printf "%-28s %s\n" "$p" "$code"
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
} > "$OUT/01_http_smoke.txt"

# 02) All routes (with middleware)
"$PHP_BIN" artisan route:list

# 03) Auth-related routes subset
"$PHP_BIN" artisan route:list
  | egrep -i 'login|logout|register|profile|dashboard|org' \
  > "$OUT/03_auth_routes.txt" || true

# 04) POST handlers for /login and /org/login
{
  VOL="$("$PHP_BIN" artisan route:list | awk '/POST/ && $2=="login"{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
  ORG="$("$PHP_BIN" artisan route:list | awk '/POST/ && $2 ~ /\/org\/login/{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
  echo "POST /login      -> ${VOL:-not-found}"
  echo "POST /org/login  -> ${ORG:-not-found}"
} > "$OUT/04_login_handlers.txt"

# 05) Dump controller sources for those handlers
dump_controller () {
  local action="$1"
  [ -z "${action:-}" ] && return 0
  local cls="${action%@*}"
  local file="app/$(echo "$cls" | sed -E 's#^App\\\\##; s#\\\\#/#g').php"
  {
    echo "--- $action => $file"
    if [ -f "$file" ]; then
      nl -ba "$file" | sed -n '1,200p'
      echo ">>> redirect/intended/HOME/profile/org/dashboard grep:"
      grep -nE "redirect\(|intended\(|RouteServiceProvider::HOME|/profile|/org/dashboard" "$file" || true
      echo
    else
      echo "!! MISSING $file"
    fi
  } >> "$OUT/05_controller_sources.txt"
}
: > "$OUT/05_controller_sources.txt"
dump_controller "$VOL"
dump_controller "$ORG"

# 06) RouteServiceProvider::HOME
{
  grep -n "const HOME" app/Providers/RouteServiceProvider.php || true
  nl -ba app/Providers/RouteServiceProvider.php | sed -n '1,120p' || true
} > "$OUT/06_home_const.txt"

# 07) RedirectIfAuthenticated middleware
RIA="$(grep -RIl --include='*.php' 'class RedirectIfAuthenticated' app/Http/Middleware || true)"
{
  echo "File: ${RIA:-not-found}"
  [ -n "$RIA" ] && nl -ba "$RIA" | sed -n '1,200p'
} > "$OUT/07_redirect_if_auth.txt" 2>/dev/null

# 08) Grep controllers for redirects / targets
grep -RIn --include='*.php' -E "redirect\(|intended\(|/profile|/org/dashboard|RouteServiceProvider::HOME" app/Http/Controllers \
  > "$OUT/08_redirect_grep.txt" || true

# 09) Profile routes
"$PHP_BIN" artisan route:list| egrep -i '(^GET|HEAD).*profile' \
  > "$OUT/09_profile_route.txt" || true

# 10) Auth config snapshot
nl -ba config/auth.php | sed -n '1,240p' > "$OUT/10_auth_config.txt" || true

# 11) Org auth controllers inventory
{
  ls -l app/Http/Controllers/Org 2>/dev/null || true
  grep -RIn --include='*.php' -E "class\s+.*Auth|function\s+(login|authenticate|store)\s*\(" app/Http/Controllers/Org 2>/dev/null || true
} > "$OUT/11_org_auth_controllers.txt"

# 12) Risky DB usage in blades
grep -RIn --include='*.blade.php' -E 'DB::|->get\(|->first\(|->count\(|->paginate\(' resources 2>/dev/null \
  > "$OUT/12_blade_db_uses.txt" || echo "none" > "$OUT/12_blade_db_uses.txt"

# 13) Hardcoded redirect targets
grep -RIn --include='*.php' -E "['\"]/profile['\"]|['\"]/org/dashboard['\"]" app 2>/dev/null \
  > "$OUT/13_hardcoded_targets.txt" || true

# 14) Latest log tail
{
  LOG="$(ls -t storage/logs/laravel*.log 2>/dev/null | head -n1 || true)"
  echo "Log: ${LOG:-none}"
  [ -n "$LOG" ] && tail -n 200 "$LOG" || true
} > "$OUT/14_log_tail.txt"

echo "AUDIT COMPLETE → $OUT"
ls -1 "$OUT"
