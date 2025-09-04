#!/usr/bin/env bash
set -euo pipefail
BASE="${BASE:-https://swaeduae.ae}"
EMAIL="${EMAIL:-}"
PASS="${PASS:-}"
SLUG="${SLUG:-}"

green=$'\e[32m'; red=$'\e[31m'; yellow=$'\e[33m'; reset=$'\e[0m'
HIT(){ echo "${green}[HIT]${reset} $*"; }
MISS(){ echo "${red}[MISS]${reset} $*"; }
WARN(){ echo "${yellow}[WARN]${reset} $*"; }

CJ="$(mktemp /tmp/swaeduae.phase2.XXXX.cookies)"
UA="Mozilla/5.0"

echo "=== Phase 2 Check: Opportunities + Apply ($(date -u +"%F %T") UTC) ==="
echo "BASE=$BASE"

# 1) Public list page
code=$(curl -s -o /dev/null -w "%{http_code}" -A "$UA" -I "$BASE/opportunities")
[[ "$code" == "200" ]] && HIT "/opportunities -> 200" || { MISS "/opportunities -> $code (expected 200)"; exit 1; }

# 2) Detect a slug if not provided
if [[ -z "${SLUG}" ]]; then
  html=$(curl -s -A "$UA" "$BASE/opportunities" | tr -d '\r')
  SLUG=$(printf "%s" "$html" | sed -n 's|.*href="/opportunities/\([^"?#]\+\)".*|\1|p' | head -n1 || true)
  if [[ -n "$SLUG" ]]; then
    HIT "Detected slug: $SLUG"
  else
    WARN "Could not auto-detect a slug from /opportunities. Set SLUG=your-slug and re-run."; exit 2
  fi
else
  echo "[INFO] Using provided SLUG=$SLUG"
fi

# 3) Public show page
code=$(curl -s -o /dev/null -w "%{http_code}" -A "$UA" -I "$BASE/opportunities/$SLUG")
[[ "$code" == "200" ]] && HIT "/opportunities/$SLUG -> 200" || { MISS "/opportunities/$SLUG -> $code (expected 200)"; exit 3; }

# 4) Login (session cookie)
login_html=$(curl -s -c "$CJ" -A "$UA" "$BASE/login")
tok=$(printf "%s" "$login_html" | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p')
if [[ -z "${EMAIL}" || -z "${PASS}" ]]; then
  WARN "EMAIL/PASS not set; skipping auth-only checks."; exit 0
fi
login_code=$(curl -s -b "$CJ" -c "$CJ" -A "$UA" -e "$BASE/login" -L "$BASE/login" \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  --data-urlencode "_token=$tok" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASS" \
  -o /dev/null -w "%{http_code}")
[[ "$login_code" == "302" || "$login_code" == "200" ]] && HIT "login flow -> $login_code" || { MISS "login failed ($login_code)"; exit 4; }

apps_code=$(curl -s -b "$CJ" -A "$UA" -I "$BASE/account/applications" -o /dev/null -w "%{http_code}")
[[ "$apps_code" == "200" ]] && HIT "/account/applications (authed) -> 200" || WARN "/account/applications -> $apps_code"

# 5) Show page (authed) should contain an Apply form targeting the route
show=$(curl -s -b "$CJ" -A "$UA" "$BASE/opportunities/$SLUG")
if printf "%s" "$show" | grep -qE 'action="/opportunities/[^"]+/apply"'; then
  HIT "Apply form found on show page"
else
  WARN "No Apply form detected in HTML (check your Blade for @auth form)"
fi

# 6) Route mapping for Apply
map=$(php artisan route:list 2>/dev/null | grep -E "opportunities/\{slug\}/apply" || true)
if [[ -n "$map" ]]; then
  echo "$map" | sed 's/^/[ROUTE] /'
  echo "$map" | grep -q "Public\\\ApplyController@store" \
    && HIT "Apply route uses Public\\ApplyController@store" \
    || WARN "Apply route not mapped to Public\\ApplyController@store"
else
  MISS "Apply route not registered (opportunities/{slug}/apply)"
fi

echo "=== Phase 2 check complete ==="
