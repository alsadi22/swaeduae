#!/usr/bin/env bash
set -euo pipefail
APP_BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_BASE"
TS=$(date +%F_%H%M%S)
OUT="public/health/full-$TS.txt"; mkdir -p public/health
FAIL=0

# small helpers
phpc(){ php artisan "$@" 2>&1; }
hit(){ curl -s -o /dev/null -w "%{http_code}" "$1"; }

{
  echo "=== FULL HEALTH $TS ==="

  echo; echo "== CACHES REFRESH =="; phpc optimize:clear >/dev/null || true
  phpc config:cache >/dev/null || true; phpc route:cache >/dev/null || true; phpc view:cache >/dev/null || true
  echo "caches: refreshed"

  echo; echo "== CONFIG SNAPSHOT =="
  read QUEUE DB LOG <<<$(php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo config("queue.default")." ".config("database.default")." ".config("logging.default");')
  echo "Queue: $QUEUE"
  echo "DB:    $DB"
  echo "Log:   $LOG"
  [[ "$QUEUE" == "database" ]] || FAIL=1
  [[ "$DB" == "mysql" ]] || FAIL=1
  [[ "$LOG" == "daily" ]] || FAIL=1

  echo; echo "== ABOUT =="; phpc about

  echo; echo "== ROUTES (top) =="; phpc route:list | sed -n '1,80p'

  echo; echo "== MIGRATIONS STATUS =="; phpc migrate:status

  echo; echo "== QUEUE & SCHEDULER ==";
  qstatus=$(systemctl is-active swaed-queue.service 2>/dev/null || echo "unknown")
  echo "queue_service=$qstatus"
  [[ "$qstatus" == "active" ]] || FAIL=1
  sudo systemctl status swaed-queue.service --no-pager --lines 6 2>/dev/null || true
  phpc schedule:run -vvv | sed -n '1,60p'

  echo; echo "== HTTP PROBES ==";
  for u in / /about /privacy /terms /org/login; do
    code=$(hit https://swaeduae.ae$u)
    echo "GET $u -> $code"
    [[ "$code" == "200" ]] || FAIL=1
  done
  curl -s -I -L https://swaeduae.ae/admin | sed -n '1,6p'

    echo; echo "== QR VERIFY CHECKS ==";
    qr=$(hit https://swaeduae.ae/qr/verify)
    echo "/qr/verify -> $qr"
    [[ "$qr" == "200" ]] || FAIL=1
    echo "/qr/verify?code=test -> $(curl -s -I -L https://swaeduae.ae/qr/verify?code=test | sed -n '1,3p' | tr '\n' ' ' )"
    echo -n "throttle spray: "; c=(); for i in $(seq 1 31); do c+=("$(hit https://swaeduae.ae/qr/verify)"); done; printf "%s\n" "${c[*]}"

  echo; echo "== CERT PDF sanity =="; ls -lh storage/app/certificates | tail -n 3 || true

  echo; echo "== PWA (manifest/SW/icons present & non-zero) ==";
  for f in public/manifest.json public/service-worker.js public/img/icon-192.png public/img/icon-512.png; do
    if [ -s "$f" ]; then echo "OK $(ls -l "$f")"; else echo "MISSING/EMPTY $f"; fi
  done

  echo; echo "== ANALYTICS on homepage ==";
  curl -s https://swaeduae.ae/ | grep -iE 'analytics:\s*plausible|plausible\.io/js/script\.js' && echo "analytics OK" || echo "analytics MISSING"

  echo; echo "== MAIL & SESSION SNAPSHOT ==";
  grep -E '^(MAIL_MAILER|MAIL_HOST|MAIL_PORT|MAIL_USERNAME|MAIL_ENCRYPTION|MAIL_FROM_ADDRESS)=' .env
  grep -E '^(SESSION_DRIVER|SESSION_LIFETIME|SESSION_SECURE_COOKIE|SESSION_SAME_SITE)=' .env

  echo; echo "== NGINX & CLOUDFLARED ==";
  sudo nginx -t 2>&1 | sed -n '1,3p'
  sudo systemctl status cloudflared --no-pager --lines 5 2>/dev/null || true

  echo; echo "== BACKUPS ==";
  sudo ls -lh /root/backups/code 2>/dev/null | tail -n 4 || true
  sudo ls -lh /root/backups/db   2>/dev/null | tail -n 4 || true

  echo; echo "== RECENT LOG ERRORS (tail) ==";
  latest=$(ls -1t storage/logs/*.log 2>/dev/null | head -1); echo "latest_log=$latest"
  [ -n "${latest:-}" ] && { tail -n 120 "$latest" | egrep -i "ERROR|EXCEPTION|LogicException|QueryException" || true; }

} | tee "$OUT"
echo "Full health saved to: $OUT"
[ "$FAIL" -eq 0 ] || exit 1
