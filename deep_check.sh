#!/usr/bin/env bash
# Deep project checkup v2.2 (read-only). Finds the Laravel app root, then runs:
# - PHP syntax checks
# - Missing includes/layouts check
# - route:list + middleware gaps
# - HTTP smoke tests
set -euo pipefail

STAMP="$(date +%F-%H%M%S)"
OUT_DIR="tmp/check-$STAMP"
mkdir -p "$OUT_DIR"

log(){ echo -e "$*"; echo -e "$*" >> "$OUT_DIR/summary.txt"; }

# --- Find Laravel app root (directory that contains "artisan")
APP_DIR="${APP_ROOT:-}"
if [ -z "${APP_DIR}" ] || [ ! -f "${APP_DIR}/artisan" ]; then
  if [ -f "artisan" ]; then
    APP_DIR="$PWD"
  else
    APP_DIR="$(find "$PWD" -maxdepth 4 -type f -name artisan -printf '%h\n' | head -n1 || true)"
  fi
fi

if [ -z "${APP_DIR}" ] || [ ! -f "${APP_DIR}/artisan" ]; then
  log "FATAL: Could not find Laravel app root (artisan not found)."
  log "Hint: run again from your Laravel root or set APP_ROOT=/path/to/app"
  exit 1
fi

log "=== DEEP CHECK v2.2 ($(date)) ==="
log "App root: $APP_DIR"

pushd "$APP_DIR" >/dev/null

# --- PHP syntax check
if php -l routes/web.php >"$OUT_DIR/routes.lint" 2>&1; then
  log "[OK] routes/web.php syntax clean"
else
  log "[FAIL] routes/web.php has syntax errors"
  cat "$OUT_DIR/routes.lint" >> "$OUT_DIR/summary.txt"
fi

# --- Critical Blade includes/layouts presence
log "\n--- Blade includes/layouts (critical) ---"
CRIT=( "resources/views/layouts/app.blade.php"
       "resources/views/layouts/guest.blade.php"
       "resources/views/partials/navbar.blade.php"
       "resources/views/components/lang-toggle.blade.php" )
missing=0
for f in "${CRIT[@]}"; do
  if [ ! -f "$f" ]; then log "Missing: $f"; missing=1; fi
done
[ $missing -eq 0 ] && log "All critical layouts/includes present."

# --- Routes + middleware audit
log "\n=== artisan route:list ==="
if php artisan route:list || true --columns=Method,URI,Name,Middleware > "$OUT_DIR/routes.txt" 2>"$OUT_DIR/artisan.err"; then
  head -n 40 "$OUT_DIR/routes.txt" >> "$OUT_DIR/summary.txt"
else
  log "[FAIL] php artisan route:list || true failed."
  log "stderr:"
  sed -n '1,120p' "$OUT_DIR/artisan.err" >> "$OUT_DIR/summary.txt"
fi

# Flag admin/org routes missing 'auth'
log "\n--- Potential auth gaps under /admin or /org ---"
if [ -s "$OUT_DIR/routes.txt" ]; then
  awk 'BEGIN{hdr=1}
       !hdr && ($2 ~ /^\/(admin|org)\//) {
         if (index($0,"auth") == 0) print
       }
       /^Method/ {hdr=0}' "$OUT_DIR/routes.txt" >> "$OUT_DIR/summary.txt" || true
else
  log "(routes.txt empty; skipping auth gap check)"
fi

# --- HTTP smoke test (localhost)
log "\n--- HTTP smoke test ---"
urls=(/ /faq /about /contact /opportunities /verify /qr/verify)
for u in "${urls[@]}"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" "http://127.0.0.1${u}" || true)
  log "$(printf '%-20s -> %s' "$u" "$code")"
done

popd >/dev/null
log "\nSummary saved to $OUT_DIR/summary.txt"
echo "Report: $OUT_DIR/summary.txt"
