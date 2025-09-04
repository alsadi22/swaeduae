#!/usr/bin/env bash
set -u
TS="$(date +%Y%m%d-%H%M%S)"
OUT="tools/reports/healthcheck_${TS}.txt"
DOMAIN="https://swaeduae.ae"
mkdir -p tools/reports
{
  echo "=== SwaedUAE Health Check ==="
  echo "Started: $(date -u) UTC | $(TZ=Asia/Dubai date) Asia/Dubai"
  echo
  echo "== PHP & Laravel =="
  php -v | head -n 2
  php artisan --version || true
  echo
  echo "== Laravel about (env/cache/drivers) =="
  php artisan about --only=environment,cache,drivers || true
  echo
  echo "== Key .env flags (safe subset) =="
  egrep -n "^(APP_ENV|APP_DEBUG|APP_URL|APP_TIMEZONE|CACHE_DRIVER|SESSION_DRIVER|QUEUE_CONNECTION)=" .env || true
  echo
  echo "== Routes (auth/org/events) =="
  php artisan route:list | egrep -i "^GET +/events|^GET +/events/|events\.index|events\.show|(^| )GET +/signin|POST +/login|POST +/logout$|GET +/register|POST +/register| org/" || true
  echo
  echo "== Guardrails =="
  if php artisan route:list | awk '$2=="GET" && $3 ~ /^logout$/ {f=1} END{exit !f}'; then echo "[FAIL] Found GET /logout"; else echo "[OK] No GET /logout route."; fi
  echo
  echo "== Migrations =="
  php artisan migrate:status || echo "[WARN] migrate:status failed."
  echo
  echo "== Scheduler =="
  php artisan schedule:list || echo "[WARN] schedule:list failed."
  echo
  echo "== Backups present? (storage/backups) =="
  ls -lh storage/backups 2>/dev/null | sed "s/^/  /" || echo "[WARN] backups dir missing."
  echo
  echo "== Recent errors (last 200 lines) =="
  tail -n 200 storage/logs/laravel*.log 2>/dev/null | egrep -i "ERROR|CRITICAL|EXCEPTION" || echo "[OK] No recent ERROR/CRITICAL lines."
  echo
  echo "== HTTP exposure checks =="
  code=$(curl -sI "$DOMAIN/.env" | awk "NR==1{print \$2}"); echo "/.env -> HTTP $code"; [ "$code" = "200" ] && echo "[FAIL] .env is publicly accessible!" || echo "[OK] .env not accessible."
  code=$(curl -sI "$DOMAIN/vendor/" | awk "NR==1{print \$2}"); echo "/vendor/ -> HTTP $code"
  code=$(curl -sI "$DOMAIN/storage/" | awk "NR==1{print \$2}"); echo "/storage/ -> HTTP $code"
  echo
  echo "== MicroCache / ETag (/events) =="
  curl -sI "$DOMAIN/events" | egrep -i "HTTP/|ETag|Cache-Control|X-MicroCache" | sed "s/^/  1st: /"
  sleep 1
  curl -sI "$DOMAIN/events" | egrep -i "HTTP/|ETag|Cache-Control|X-MicroCache" | sed "s/^/  2nd: /"
  echo
  echo "== i18n presence =="
  [ -f resources/lang/en.json ] && echo "[OK] en.json present" || echo "[WARN] missing en.json"
  [ -f resources/lang/ar.json ] && echo "[OK] ar.json present" || echo "[WARN] missing ar.json"
  echo
  echo "== Permissions =="
  [ -w storage ] && echo "[OK] storage/ writable" || echo "[FAIL] storage/ not writable"
  [ -w bootstrap/cache ] && echo "[OK] bootstrap/cache writable" || echo "[FAIL] bootstrap/cache not writable"
} | tee "$OUT"
echo "Saved report -> $OUT"
