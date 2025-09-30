PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/swaeduae"             # absolute app path
PHP_BIN="${PHP:-php}"
HOST="swaeduae.ae"
BASE="http://127.0.0.1"
TOK="$(sed -n 's/^AGENT_TOKEN=\(.*\)$/\1/p' "$APP/.env" | tr -d '\r\n')"

TS="$(date +%Y-%m-%dT%H:%M:%S%z)"
LOG="$APP/storage/logs/agent_guard_$(date +%Y%m%d-%H%M%S).log"
FAIL=0

log(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }

cd "$APP"
log "=== Agent Guard @ $TS ==="
log "PHP: $($PHP_BIN -v | head -n1)"
log "APP: $APP"
log "Token present: $([ -n "$TOK" ] && echo yes || echo no)"

# 0) Enforce NO-APPLY (warn/fail if enabled)
ALLOW="$(sed -n 's/^AGENT_ALLOW_APPLY=\(.*\)$/\1/p' "$APP/.env" | tr -d '\r\n' | tr '[:upper:]' '[:lower:]')"
if [ "$ALLOW" = "1" ] || [ "$ALLOW" = "true" ]; then
  log "ERROR: AGENT_ALLOW_APPLY is enabled; set it empty/false."
  FAIL=1
fi

# 1) Audit (read-only)
if "$PHP_BIN" artisan agent:audit >>"$LOG" 2>&1; then
  log "audit: OK"
else
  log "audit: FAILED"
  FAIL=1
fi

# 2) Scan (read-only): parse absolute JSON path
REP="$APP/storage/app/agent/report.json"
if "$PHP_BIN" artisan agent:scan >>"$LOG" 2>&1; then
  if [ -f "$REP" ]; then
    ISSUES="$("$PHP_BIN" -r "echo (int) (json_decode(file_get_contents('$REP'), true)['issues'] ?? 0);" 2>/dev/null || echo 0)"
    PAGES="$("$PHP_BIN" -r "echo (int) (json_decode(file_get_contents('$REP'), true)['crawler_pages'] ?? 0);" 2>/dev/null || echo 0)"
    log "scan: pages=$PAGES issues=$ISSUES"
    if [ "${PAGES:-0}" -eq 0 ]; then
      log "scan: no pages crawled — WARN only (not failing)"
    else
      [ "${ISSUES:-0}" -gt 0 ] && FAIL=1
    fi
  else
    log "scan: report.json missing — WARN only (not failing)"
  fi
else
  log "scan: FAILED"
  FAIL=1
fi

# 3) HTTP smoke checks
curl_code(){ curl -s -o /dev/null -w "%{http_code}" --max-time 6 -H "Host: $HOST" "$@" || echo 000; }
need_ok(){ local code="$1" pat="$2"; [[ "$code" =~ ^$pat$ ]]; }
check(){
  local path="$1" expect="$2"; shift 2 || true
  local code; code="$(curl_code "$BASE$path" "$@")"
  log "http $path -> $code (need $expect)"
  need_ok "$code" "$expect" || FAIL=1
}

# token endpoints
if [ -n "$TOK" ]; then
  H=(-H "X-Agent-Token: $TOK")
  check "/healthz-agent" "(200)" "${H[@]}"
  check "/api/agent/ping" "(200)" "${H[@]}"
else
  log "WARN: No AGENT_TOKEN in .env; skipping token endpoints."
fi

# public pages (allow 200 or 302)
for p in "/" "/contact" "/about-us" "/qr/verify" "/api/v1/health"; do
  check "$p" "(200|302)"
done

# 4) Migrations status
if "$PHP_BIN" artisan migrate:status | grep -q 'Pending'; then
  log "migrate: PENDING migrations detected"
  FAIL=1
else
  log "migrate: OK"
fi

# Summary (respect NO-EXIT mode)
if [ "$FAIL" -ne 0 ]; then
  log "=== RESULT: FAIL ==="
  echo "FAIL" > "$APP/storage/app/agent/LAST_STATUS"
  if [ -n "${AGENT_GUARD_NO_EXIT:-}" ]; then
    log "NO-EXIT mode set; not exiting with error"
  else
    exit 1
  fi
else
  log "=== RESULT: OK ==="
  echo "OK" > "$APP/storage/app/agent/LAST_STATUS"
fi
