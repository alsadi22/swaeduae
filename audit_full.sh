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

# 00 env
{ echo "PWD: $(pwd)"; "$PHP_BIN" -v; } > "$OUT/00_env.txt" 2>&1

# 01 smoke
{
  echo "== QUICK HTTP SMOKE (unauth) =="
  while read -r p; do [ -z "$p" ] && continue; printf "%-28s %s\n" "$p" "$(http_code "$BASE$p")"; done <<EOF
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
} | tee "$OUT/01_http_smoke.txt"

# 02 routes (full)
"$PHP_BIN" artisan route:list --columns=Method,URI,Name,Action,Middleware \
  | tee "$OUT/02_routes.txt" >/dev/null

# 03 auth-related subset
"$PHP_BIN" artisan route:list --columns=Method,URI,Name,Action,Middleware \
  | grep -Ei 'login|logout|register|profile|dashboard|(^|/)org(/|$)' \
  | tee "$OUT/03_auth_routes.txt" >/dev/null

# 04 POST handlers for /login and /org/login
VOL="$("$PHP_BIN" artisan route:list --columns=Method,URI,Action \
      | awk '/^POST/ && $2=="login"{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
ORG="$("$PHP_BIN" artisan route:list --columns=Method,URI,Action \
      | awk '/^POST/ && $2 ~ /^org\/login$/{for(i=1;i<=NF;i++) if ($i ~ /@/) {print $i; exit}}')"
{ echo "POST /login      -> ${VOL:-not-found}"; echo "POST /org/login  -> ${ORG:-not-found}"; } \
  | tee "$OUT/04_login_handlers.txt" >/dev/null

# 05 dump controller sources
dump(){
  local action="${1:-}"; [ -z "$action" ] && return 0
  local cls="${action%@*}"; local file="app/$(echo "$cls" | sed -E 's#^App\\\\##; s#\\\\#/#g').php"
  echo -e "\n--- $action => $file" | tee -a "$OUT/05_controller_sources.txt"
  if [ -f "$file" ]; then
    nl -ba "$file" | sed -n '1,200p' | tee -a "$OUT/05_controller_sources.txt" >/dev/null
    echo ">>> redirect/intended/HOME/profile/org/dashboard grep:" | tee -a "$OUT/05_controller_sources.txt"
    grep -nE "redirect\(|intended\(|RouteServiceProvider::HOME|/profile|/org/dashboard" "$file" \
      | tee -a "$OUT/05_controller_sources.txt" >/dev/null || true
  else
    echo "!! MISSING $file" | tee -a "$OUT/05_controller_sources.txt"
  fi
}
: > "$OUT/05_controller_sources.txt"
dump "$VOL"
dump "$ORG"

# 06 RouteServiceProvider::HOME
{
  echo "== RouteServiceProvider =="
  grep -n "const HOME" app/Providers/RouteServiceProvider.php || true
  nl -ba app/Providers/RouteServiceProvider.php | sed -n '1,120p' || true
} | tee "$OUT/06_home_const.txt" >/dev/null

# 07 RedirectIfAuthenticated
RIA="$(grep -RIl --include='*.php' 'class RedirectIfAuthenticated' app/Http/Middleware || true)"
{
  echo "File: ${RIA:-not-found}"
  [ -n "$RIA" ] && nl -ba "$RIA" | sed -n '1,200p'
} | tee "$OUT/07_redirect_if_auth.txt" >/dev/null

# 08 grep controllers for redirect targets
grep -RIn --include='*.php' -E "redirect\(|intended\(|/profile|/org/dashboard|RouteServiceProvider::HOME" app/Http/Controllers \
  | tee "$OUT/08_redirect_grep.txt" >/dev/null || true

# 09 profile routes only
"$PHP_BIN" artisan route:list --columns=Method,URI,Name,Action,Middleware \
  | grep -E '(^GET|HEAD).*(^|/)profile($|/)' \
  | tee "$OUT/09_profile_route.txt" >/dev/null || true

# 10 auth config snapshot
nl -ba config/auth.php | sed -n '1,240p' | tee "$OUT/10_auth_config.txt" >/dev/null || true

# 11 blade risky db
grep -RIn --include='*.blade.php' -E 'DB::|->get\(|->first\(|->count\(|->paginate\(' resources \
  | tee "$OUT/12_blade_db_uses.txt" >/dev/null || echo "none" | tee "$OUT/12_blade_db_uses.txt"

# 12 hardcoded redirect targets
grep -RIn --include='*.php' -E "['\"]/profile['\"]|['\"]/org/dashboard['\"]" app \
  | tee "$OUT/13_hardcoded_targets.txt" >/dev/null || true

# 13 log tail
LOG="$(ls -t storage/logs/laravel*.log 2>/dev/null | head -n1 || true)"
{ echo "Log: ${LOG:-none}"; [ -n "$LOG" ] && tail -n 200 "$LOG" || true; } \
  | tee "$OUT/14_log_tail.txt" >/dev/null

echo
echo "AUDIT COMPLETE → $OUT"
