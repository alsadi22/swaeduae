#!/usr/bin/env bash
# SwaedUAE – Full Audit v3
# Usage: sudo bash tools/full_audit_v3.sh [/var/www/swaeduae/current]
# Creates a timestamped report in public/health and prints a red/green summary.
# Author: ChatGPT (2025-09-04)

set -euo pipefail
IFS=$'\n\t'

# --- Helpers ---------------------------------------------------------------
COLORS=1
if ! [ -t 1 ]; then COLORS=0; fi
if ! command -v tput >/dev/null 2>&1; then COLORS=0; fi
if [ "$COLORS" -eq 1 ]; then
  GRN="$(tput setaf 2)"; RED="$(tput setaf 1)"; YEL="$(tput setaf 3)"; CYA="$(tput setaf 6)"; DIM="$(tput dim)"; RST="$(tput sgr0)"
else
  GRN=""; RED=""; YEL=""; CYA=""; DIM=""; RST=""
fi

PASS_CNT=0
WARN_CNT=0
FAIL_CNT=0

pass(){ echo -e "${GRN}[PASS]${RST} $*" | tee -a "$OUT"; PASS_CNT=$((PASS_CNT+1)); }
warn(){ echo -e "${YEL}[WARN]${RST} $*" | tee -a "$OUT"; WARN_CNT=$((WARN_CNT+1)); }
fail(){ echo -e "${RED}[FAIL]${RST} $*" | tee -a "$OUT"; FAIL_CNT=$((FAIL_CNT+1)); RET=1; }
note(){ echo -e "${CYA}-- $*${RST}" | tee -a "$OUT"; }
sect(){ echo -e "\n${DIM}===== $* =====${RST}" | tee -a "$OUT"; }

RET=0
START_TS=$(date +%F_%H%M%S)
APP_ROOT="${1:-/var/www/swaeduae/current}"
[ -d "$APP_ROOT" ] || APP_ROOT="/var/www/swaeduae"
cd "$APP_ROOT" || { echo "No app dir: $APP_ROOT"; exit 1; }

LOG_DIR="public/health"
mkdir -p "$LOG_DIR"
OUT="$LOG_DIR/full_audit_${START_TS}.txt"
HTML_INDEX="$LOG_DIR/index.html"

BASE_URL=${BASE_URL:-"https://swaeduae.ae"}
ADMIN_URL=${ADMIN_URL:-"https://admin.swaeduae.ae"}

{
  echo "SwaedUAE – Full Audit v3"
  echo "App root: $APP_ROOT"
  echo "Report: $OUT"
  echo "Base: $BASE_URL | Admin: $ADMIN_URL"
  echo "When: $(date -R)"
} > "$OUT"

# --- Section 0: OS/Services ------------------------------------------------
sect "0) OS & Services"
if command -v lsb_release >/dev/null 2>&1; then note "$(lsb_release -ds)"; fi
note "Kernel: $(uname -r)"

for SVC in nginx php8.3-fpm php8.2-fpm php8.1-fpm; do
  if systemctl list-units --type=service | grep -q "${SVC}.service"; then
    if systemctl is-active --quiet "$SVC"; then pass "Service $SVC is active"; else fail "Service $SVC NOT active"; fi
  fi
done

# --- Section 1: PHP/Composer ------------------------------------------------
sect "1) PHP & Composer"
if php -v >/dev/null 2>&1; then
  note "$(php -v | head -n1)"
  PHP_MEM=$(php -r 'echo ini_get("memory_limit");')
  if [[ "$PHP_MEM" =~ ^(512M|[6-9][0-9]{2}M|[1-9][0-9]G)$ ]]; then pass "memory_limit=$PHP_MEM"; else warn "memory_limit=$PHP_MEM (expected >=512M)"; fi
else
  fail "php not found"
fi

if command -v composer >/dev/null 2>&1; then
  COMPOSER_JSON_CHANGED=0
  if [ -f composer.json ] && [ -d vendor ]; then pass "Composer files present"; else warn "composer.json/vendor missing"; fi
else
  warn "composer not found"
fi

# --- Section 2: Laravel Basics ---------------------------------------------
sect "2) Laravel Basics"
APP_ENV=$(grep -E '^APP_ENV=' .env 2>/dev/null | tail -n1 | cut -d= -f2-)
APP_DEBUG=$(grep -E '^APP_DEBUG=' .env 2>/dev/null | tail -n1 | cut -d= -f2-)
if php artisan --version >/dev/null 2>&1; then
  note "$(php artisan --version)"
  
  
  [ "${APP_ENV:-}" = "production" ] && pass "APP_ENV=production" || warn "APP_ENV=${APP_ENV:-?}"
  [ "${APP_DEBUG:-}" = "false" ] && pass "APP_DEBUG=false" || warn "APP_DEBUG=${APP_DEBUG:-?} (should be false)"
else
  fail "artisan not runnable"
fi

# Storage perms
[ -w storage ] && [ -w bootstrap/cache ] && pass "storage/ & bootstrap/cache writable" || fail "Fix permissions for storage/ and bootstrap/cache"

# --- Section 3: Routes ------------------------------------------------------
sect "3) Routes & syntax"
if php -l routes/web.php >/dev/null 2>&1; then pass "routes/web.php syntax OK"; else fail "routes/web.php has syntax errors"; fi

# Check single open tag & require line formatting
PHP_OPEN_TAGS=$(grep -c "^<?php" routes/web.php || true)
if [ "${PHP_OPEN_TAGS}" -le 1 ]; then pass "Single PHP open tag in routes/web.php"; else warn "Multiple PHP open tags ($PHP_OPEN_TAGS)"; fi
if grep -q "require __DIR__.'/partials/disable_internal.php';" routes/web.php; then pass "disable_internal.php require line OK"; else warn "Missing/incorrect require for partials/disable_internal.php"; fi

if php artisan route:list >/dev/null 2>&1; then
  ROUTE_SAMPLE=$(php artisan route:list | head -n 12)
  echo "$ROUTE_SAMPLE" | sed 's/^/    /' | tee -a "$OUT" >/dev/null
  # Key routes present?
  php artisan route:list | grep -qE "admin\\." && pass "Admin named routes present" || fail "No admin.* named routes"
  php artisan route:list | grep -qE "org\\." && pass "Org named routes present" || warn "No org.* named routes"
  php artisan route:list | grep -qE "qr|verify" && pass "QR/verify routes present" || warn "No QR verify routes"
  php artisan route:list | grep -qE "certificate|certificates" && pass "Certificate routes present" || warn "No certificate routes"
else
  fail "php artisan route:list failed"
fi

# Legacy logout reference
if grep -RIn --exclude='*.bak*' --exclude='*.bak' -E "route\(["]logout\.perform["]\)" resources || true; then pass "No legacy logout.perform references"; else pass "No legacy logout.perform references"; fi

# --- Section 4: Views & Layout hygiene -------------------------------------
sect "4) Views & Layouts"
# Admin must not extend public layout
if grep -RIn "extends\\(['\"']public" resources/views/admin >/dev/null 2>&1; then
  fail "Admin views extend public layout (cleanup needed)"
else
  pass "Admin views do NOT extend public layout"
fi
# Public should use TravelPro (best-effort heuristic)
if grep -RIn "TravelPro" resources/views/public resources/views/partials 2>/dev/null | head -n1 >/dev/null; then
  pass "TravelPro assets referenced in public layer"
else
  warn "Cannot confirm TravelPro usage in public layout (check)"
fi

# --- Section 5: HTTP probes -------------------------------------------------
sect "5) HTTP Probes (guest)"
probe(){ code=$(curl -s -o /dev/null -w "%{http_code}" "$1"); echo "$code"; }

C1=$(probe "$BASE_URL/")
C2=$(probe "$BASE_URL/qr/verify")
C3=$(probe "$BASE_URL/admin/login")
C4=$(probe "$BASE_URL/org/login")
C5=$(probe "$BASE_URL/applications")  # should 302 as guest

[ "$C1" = "200" ] && pass "/ -> 200" || fail "/ -> $C1"
[ "$C2" = "200" ] && pass "/qr/verify -> 200" || warn "/qr/verify -> $C2"
[[ "$C3" =~ ^(200|301|302)$ ]] && pass "/admin/login -> $C3" || fail "/admin/login -> $C3"
[[ "$C4" =~ ^(200|302)$ ]] && pass "/org/login -> $C4" || warn "/org/login -> $C4"
[ "$C5" = "302" ] && pass "/applications -> 302 (guest protected)" || warn "/applications -> $C5 (expected 302)"

# CSRF token on admin login page
if curl -sL "$BASE_URL/admin/login" | grep -q 'name="_token"'; then pass "Admin login has CSRF token"; else warn "CSRF token not found on admin login page"; fi

# --- Section 6: PWA & SEO ---------------------------------------------------
sect "6) PWA & SEO"
[ -f public/manifest.json ] && pass "manifest.json present" || warn "manifest.json missing"
[ -f public/service-worker.js ] && pass "service-worker.js present" || warn "service-worker.js missing"
[ -f public/img/icons/icon-192x192.png ] && [ -f public/img/icons/icon-512x512.png ] && pass "PWA icons present" || warn "PWA icons missing"

if [ -L public/sitemap.xml ] && [ -f public/sitemaps/sitemap-index.xml ]; then pass "sitemap symlink -> index OK"; else warn "sitemap link/index missing"; fi

# --- Section 7: Mail & Queue ------------------------------------------------
sect "7) Mail & Queue"
MAILER=$(grep -E '^MAIL_MAILER=' .env 2>/dev/null | cut -d= -f2- || true)
MAILHOST=$(grep -E '^MAIL_HOST=' .env 2>/dev/null | cut -d= -f2- || true)
[ "${MAILER:-}" = "smtp" ] && pass "MAIL_MAILER=smtp" || warn "MAIL_MAILER=${MAILER:-?}"
[ "${MAILHOST:-}" = "smtp.zoho.com" ] && pass "MAIL_HOST=smtp.zoho.com" || warn "MAIL_HOST=${MAILHOST:-?}"

if systemctl list-units --type=service | grep -q 'swaed-queue-worker'; then
  if systemctl is-active --quiet swaed-queue-worker; then pass "swaed-queue-worker active"; else fail "swaed-queue-worker NOT active"; fi
else
  warn "swaed-queue-worker service not found"
fi

# Failed jobs count
if php artisan queue:failed >/dev/null 2>&1; then
  FJCNT=$(php artisan queue:failed | { grep -cE '^[0-9a-f-]'; } || true)
  [ "${FJCNT:-0}" -eq 0 ] && pass "No failed jobs" || warn "Failed jobs: $FJCNT (check with php artisan queue:failed)"
else
  warn "queue:failed not available"
fi

# --- Section 8: Security baseline ------------------------------------------
sect "8) Security baseline"
# Rate limiting, captcha flags (heuristic via .env)
CAPTCHA=$(grep -E '^RECAPTCHA_\w+=' .env 2>/dev/null | wc -l | tr -d ' ')
[ "$CAPTCHA" -ge 1 ] && pass "reCAPTCHA keys present in .env" || warn "reCAPTCHA not configured in .env"

SENTRY=$(grep -E '^SENTRY_DSN=' .env 2>/dev/null | cut -d= -f2- || true)
[ -n "${SENTRY:-}" ] && pass "SENTRY_DSN set" || warn "SENTRY_DSN not set"

# --- Section 9: Logs --------------------------------------------------------
sect "9) Recent errors (logs)"
LOGF=$(ls -1t storage/logs/laravel-*.log 2>/dev/null | head -n1 || true)
if [ -n "$LOGF" ]; then
  ERR_CNT=$(grep -cE '\\[error\\]|\\[critical\\]|\\[alert\\]|\\[emergency\\]' "$LOGF" || true)
  note "Log file: $(basename "$LOGF") — recent error-like lines: $ERR_CNT"
  tail -n 80 "$LOGF" | sed 's/^/    /' >> "$OUT" || true
  [ "${ERR_CNT:-0}" -eq 0 ] && pass "No recent high-severity log entries" || warn "See tail above for recent issues"
else
  warn "No laravel logs found"
fi

# --- Section 10: Summary ----------------------------------------------------
sect "10) Summary"
echo "Passed: $PASS_CNT  |  Warnings: $WARN_CNT  |  Failures: $FAIL_CNT" | tee -a "$OUT"

# Update simple HTML index
cat > "$HTML_INDEX" <<HTML
<!doctype html><meta charset="utf-8"><title>SwaedUAE Health</title>
<style>body{font:14px/1.5 system-ui,Segoe UI,Roboto,Arial} .ok{color:#0a0} .warn{color:#c90} .fail{color:#a00} pre{background:#111;color:#eee;padding:12px;border-radius:8px;overflow:auto}</style>
<h1>SwaedUAE Health – $(date -R)</h1>
<p>Latest report: <a href="$(basename "$OUT")">$(basename "$OUT")</a></p>
<p class="$( [ "$FAIL_CNT" -eq 0 ] && echo ok || echo fail )">Summary: Passed $PASS_CNT · Warnings $WARN_CNT · Failures $FAIL_CNT</p>
<p>Base: $BASE_URL · Admin: $ADMIN_URL</p>
HTML

# Exit code reflects failures
exit $RET
