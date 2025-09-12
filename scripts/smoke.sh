#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

source scripts/smoke.env

CURL="curl -sS"
CJAR="$(mktemp)"
LOG="storage/logs/smoke.$(date +%F_%H%M%S).log"
PASS=0; FAIL=0

finish(){ rm -f "$CJAR"; echo "SUMMARY: $PASS passed, $FAIL failed"; test "$FAIL" -eq 0; }
trap finish EXIT

log(){ echo "[$(date +%T)] $*" | tee -a "$LOG"; }

# HEAD/GET checker that accepts 200 or 302 for page reachability
check_200_or_302(){
  local path="$1"; local name="${2:-$1}"
  local code
  code=$(curl -s -o /dev/null -w "%{http_code}" -H "Accept-Language: ${ACCEPT_LANG:-ar}" "${BASE_URL}${path}")
  if [[ "$code" == "200" || "$code" == "302" ]]; then
    log "✔ ${code} ${name} (${path})"
    PASS=$((PASS+1))
  else
    log "✖ ${code} ${name} (${path})"
    FAIL=$((FAIL+1))
  fi
}

# Trace redirect chain (without following) to debug loops
trace_redirects(){
  local url="$1"
  log "Tracing redirects for: $url"
  curl -s -I -L --max-redirs 10 -o /dev/null -w $'Final: %{url_effective}\nCode: %{http_code}\nRedirects: %{num_redirects}\n' "$url" | tee -a "$LOG"
}

# Resolve the actual login path after at most one redirect
detect_login_path(){
  # If /login redirects (e.g., to /ar/login), capture the Location once
  local location
  location=$(curl -s -D - -o /dev/null "${BASE_URL}/login" | awk 'BEGIN{IGNORECASE=1}/^Location:/{print $2; exit}' | tr -d '\r')
  if [[ -n "$location" ]]; then
    # Turn absolute to relative
    printf "%s" "${location#${BASE_URL}}"
  else
    printf "/login"
  fi
}

login(){
  log "Logging in as ${OWNER_EMAIL}"
  trace_redirects "${BASE_URL}/login"

  local LOGIN_PATH
  LOGIN_PATH="$(detect_login_path)"
  log "Using login path: ${LOGIN_PATH}"

  # Fetch the login form (allow up to 10 redirects but fail fast on loops)
  local html code
  html=$(curl -s -L --max-redirs 10 -c "$CJAR" -H "Accept-Language: ${ACCEPT_LANG:-ar}" "${BASE_URL}${LOGIN_PATH}" || true)
  code=$(curl -s -o /dev/null -w "%{http_code}" -b "$CJAR" -H "Accept-Language: ${ACCEPT_LANG:-ar}" "${BASE_URL}${LOGIN_PATH}")
  if [[ "$code" != "200" ]]; then
    log "✖ Could not get login form (${code}) — likely a redirect loop or guard"
    FAIL=$((FAIL+1)); return 1
  fi

  local token
  token=$(echo "$html" | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -n1)
  if [[ -z "$token" ]]; then
    log "✖ CSRF token not found on login page"
    FAIL=$((FAIL+1)); return 1
  fi

  # Submit credentials
  code=$(curl -s -o /dev/null -w "%{http_code}" -b "$CJAR" -c "$CJAR" \
    -H "Accept-Language: ${ACCEPT_LANG:-ar}" \
    -e "${BASE_URL}${LOGIN_PATH}" \
    -d "_token=${token}" -d "email=${OWNER_EMAIL}" -d "password=${OWNER_PASSWORD}" \
    "${BASE_URL}${LOGIN_PATH}")
  if [[ "$code" =~ ^(200|302)$ ]]; then
    log "✔ Logged in (${code})"
    PASS=$((PASS+1))
  else
    log "✖ Login failed (HTTP ${code})"
    FAIL=$((FAIL+1))
  fi
}

check_auth_200(){
  local path="$1"; local name="${2:-$1}"
  local code
  code=$(curl -s -o /dev/null -w "%{http_code}" -b "$CJAR" -H "Accept-Language: ${ACCEPT_LANG:-ar}" "${BASE_URL}${path}")
  if [[ "$code" == "200" ]]; then
    log "✔ 200 ${name} (${path})"
    PASS=$((PASS+1))
  else
    log "✖ ${code} ${name} (${path})"
    FAIL=$((FAIL+1))
  fi
}

# --- PUBLIC ---
log "== PUBLIC =="
check_200_or_302 "/" "Home"
check_200_or_302 "/login" "Login"
check_200_or_302 "/register" "Register"
check_200_or_302 "/opportunities" "Opportunities (public index)"

# --- AUTH (ORG OWNER) ---
log "== AUTH (ORG OWNER) =="
if login; then
  check_auth_200 "/org/dashboard" "Org Dashboard"
  check_auth_200 "/org/settings" "Org Settings"
  check_auth_200 "/org/team" "Org Team"
  check_auth_200 "/org/kyc" "Org KYC"

  # Attendance settings of first listed opportunity (if page exists)
  if curl -s -b "$CJAR" "${BASE_URL}/org/opportunities" | grep -Eo '/org/opportunities/[0-9]+' | head -n1 >/dev/null; then
    OPP_PATH=$(curl -s -b "$CJAR" "${BASE_URL}/org/opportunities" | grep -Eo '/org/opportunities/[0-9]+' | head -n1)
    OPP_ID="${OPP_PATH##*/}"
    check_auth_200 "/org/opportunities/${OPP_ID}/attendance-settings" "Attendance Settings (opp ${OPP_ID})"
  fi
else
  log "Skipping authenticated checks due to login failure."
fi

log "Log written to: $LOG"
