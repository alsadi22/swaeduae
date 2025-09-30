PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/swaeduae"; PHP_BIN="${PHP:-php}"; COMPOSER="${COMPOSER:-composer}"
LOG="$APP/storage/logs/codex_guard_$(date +%Y%m%d-%H%M%S).log"; FAIL=0
log(){ printf "[%s] %s\n" "$(date +%H:%M:%S)" "$*" | tee -a "$LOG"; }
cd "$APP"; log "=== Codex Guard (read-only) ==="

# 0) Tool bootstrap (quiet)
if command -v $COMPOSER >/dev/null 2>&1; then
  $COMPOSER show laravel/pint >/dev/null 2>&1 || $COMPOSER require --dev -n laravel/pint:^1 >/dev/null || true
  $COMPOSER show nunomaduro/larastan >/dev/null 2>&1 || $COMPOSER require --dev -n nunomaduro/larastan:^2 phpstan/phpstan:^1 >/dev/null || true
fi

# 1) Syntax lint
log "php -l …"
if ! find app routes database config -name '*.php' -print0 2>/dev/null | xargs -0 -n1 -P4 php -l >/dev/null; then log "ERROR: PHP syntax errors"; FAIL=1; else log "php -l: OK"; fi

# 2) PHPStan
if [ -x vendor/bin/phpstan ]; then
  log "phpstan analyse …"
  vendor/bin/phpstan analyse --no-progress --memory-limit=1G >>"$LOG" 2>&1 || { log "ERROR: PHPStan found issues"; FAIL=1; }
else log "WARN: phpstan not installed (skipped)"; fi

# 3) Pint (style) — warn only
if [ -x vendor/bin/pint ]; then
  log "pint --test …"
  vendor/bin/pint --test >>"$LOG" 2>&1 || log "WARN: Pint style differences (no auto-fix)"
fi

# 4) Duplication via PHPCPD (vendor or PHAR) — warn only
if [ -x vendor/bin/phpcpd ]; then
  log "phpcpd (composer) …"
  vendor/bin/phpcpd app routes database >>"$LOG" 2>&1 || log "WARN: Duplicated code detected (see log)"
elif [ -x tools/phpcpd.phar ]; then
  log "phpcpd (phar) …"
  php tools/phpcpd.phar app routes database >>"$LOG" 2>&1 || log "WARN: Duplicated code detected (see log)"
else
  log "WARN: phpcpd not installed (skipped)"
fi

# 5) Route-name integrity (missing route('name') used in code)
log "route-name integrity …"
ROUTE_NAMES="$APP/storage/app/codex/route_names.txt"
$PHP_BIN -r '
require __DIR__."/vendor/autoload.php";
$app=require __DIR__."/bootstrap/app.php";
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();
$names=[]; foreach (app("router")->getRoutes() as $r){ if($n=$r->getName()){ $names[$n]=true; } }
ksort($names); foreach(array_keys($names) as $n){ echo $n,PHP_EOL; }' > "$ROUTE_NAMES" 2>>"$LOG" || true
USED_NAMES="$APP/storage/app/codex/route_used.txt"
grep -RhoP "route\(\s*['\"][A-Za-z0-9._-]+['\"]" app resources routes 2>/dev/null \
 | sed -E "s/.*route\(\s*['\"]([A-Za-z0-9._-]+)['\"].*/\1/" | sort -u > "$USED_NAMES" || true
MISSING="$APP/storage/app/codex/route_missing.txt"
comm -23 "$USED_NAMES" "$ROUTE_NAMES" > "$MISSING" || true
if [ -s "$MISSING" ]; then log "ERROR: missing route names ($(wc -l < "$MISSING" | tr -d ' ')):"; sed -n '1,50p' "$MISSING" | tee -a "$LOG"; FAIL=1; else log "routes: all referenced names exist"; fi

# 6) Migrations
if $PHP_BIN artisan migrate:status | grep -q 'Pending'; then log "ERROR: pending migrations"; FAIL=1; else log "migrate: OK"; fi

# 7) Summary (NO-EXIT mode respected)
if [ "$FAIL" -ne 0 ]; then log "=== RESULT: FAIL (see $LOG) ==="; echo "FAIL" > "$APP/storage/app/codex/LAST_STATUS"; [ -n "${CODEX_NO_EXIT:-}" ] && log "NO-EXIT mode set; not exiting with error" || exit 1
else log "=== RESULT: OK ==="; echo "OK" > "$APP/storage/app/codex/LAST_STATUS"; fi
