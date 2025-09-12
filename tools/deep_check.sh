#!/usr/bin/env bash
set -euo pipefail
APP_BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_BASE"
TS=$(date +%F_%H%M%S); LOGDIR="public/health"; OUT="$LOGDIR/deep-$TS.txt"; mkdir -p "$LOGDIR"
php artisan optimize:clear >/dev/null 2>&1 || true
php artisan config:cache >/dev/null 2>&1 || true
php artisan route:cache  >/dev/null 2>&1 || true
php artisan view:cache   >/dev/null 2>&1 || true
{
  echo "=== SwaedUAE DEEP CHECK $TS ==="
  echo "== ABOUT =="; php artisan about
  echo; echo "== ROUTES (top) =="; php artisan route:list | sed -n '1,50p'
  echo; echo "== MIGRATIONS STATUS =="; php artisan migrate:status
  echo; echo "== QUEUE WORKER =="; systemctl is-active swaed-queue.service 2>/dev/null || true
  sudo systemctl status swaed-queue.service --no-pager --lines 5 2>/dev/null || true
  echo; echo "== SCHEDULER DRY RUN =="; php artisan schedule:run -vvv | sed -n '1,60p'
  echo; echo "== HEALTH SCRIPTS =="; [ -x tools/health.sh ] && bash tools/health.sh || echo "tools/health.sh not found/executable"
  [ -x tools/full_health.sh ] && bash tools/full_health.sh || echo "tools/full_health.sh not found/executable"
  echo; echo "== HTTP PROBES =="; for u in / /about /privacy /terms /org/login; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae$u"); echo "GET $u -> $code"; done
  curl -s -I -L https://swaeduae.ae/admin | sed -n '1,6p'
  echo; echo "== DEV ROUTE HTTP CHECK =="; for u in _agent _alias/test _compat; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "https://swaeduae.ae/$u"); echo "$u -> $code"; done
  echo; echo "== MAIL ENV SNAPSHOT =="; grep -E '^(MAIL_MAILER|MAIL_HOST|MAIL_PORT|MAIL_USERNAME|MAIL_ENCRYPTION|MAIL_FROM_ADDRESS)=' .env
  echo; echo "== SESSION SETTINGS =="; grep -E '^(SESSION_DRIVER|SESSION_LIFETIME|SESSION_SECURE_COOKIE|SESSION_SAME_SITE)=' .env
  echo; echo "== QR ALIAS ROUTES =="; php artisan route:list | grep -E 'admin\.attendance\.qr\.(finalize|issue|reset)' || true
  echo; echo "== RECENT LOG TAIL =="; latest=$(ls -1t storage/logs/*.log 2>/dev/null | head -1); echo "latest_log=$latest"
  [ -n "${latest:-}" ] && tail -n 80 "$latest" || echo "no laravel logs found"
  echo; echo "== NGINX & CLOUDFLARED =="; sudo nginx -t 2>&1 | sed -n '1,5p'
  sudo systemctl status cloudflared --no-pager --lines 5 2>/dev/null || true
  echo; echo "== BACKUPS =="; sudo ls -lh /root/backups/code 2>/dev/null | tail -n 5 || true
  sudo ls -lh /root/backups/db 2>/dev/null | tail -n 5 || true
  echo; echo "Deep check saved to: $OUT"
} | tee "$OUT"
