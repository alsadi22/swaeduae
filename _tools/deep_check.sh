#!/usr/bin/env bash
set -euo pipefail
# Optional healthchecks.io pings
ROOT="/var/www/swaeduae"
cd "$ROOT" || exit 0
HC_URL="$(grep -m1 '^HEALTHCHECKS_URL=' .env 2>/dev/null | sed -E 's/^HEALTHCHECKS_URL=//')"
if [ -n "${HC_URL:-}" ]; then curl -fsS -m 10 -o /dev/null "$HC_URL/start" || true; fi
SITE="${SITE:-swaeduae.ae}"
STAMP="$(date +%F-%H%M%S)"
OUT="storage/logs/healthcheck-$STAMP.log"
say(){ echo -e "$*" | tee -a "$OUT"; }

say "=== SwaedUAE Healthcheck $(date) ==="
say "App info:"
php artisan about --ansi | tee -a "$OUT" || true

say "\nRoutes (first 80):"
php artisan route:list 2>/dev/null | sed -n '1,80p' | tee -a "$OUT" || true

say "\nLocal HTTP (origin):"
for p in / /about /services /contact /robots.txt /sitemap.xml ; do
  code=$(curl -s -o /dev/null -w '%{http_code}' -H "Host: $SITE" "http://127.0.0.1$p")
  say "  http://127.0.0.1$p -> $code"
done

say "\nCloudflare edge:"
curl -sI "https://$SITE/?_cf_bust=$(date +%s)" | sed -n '1,14p' | tee -a "$OUT"

say "\nSecurity headers (origin):"
curl -sI -H "Host: $SITE" "http://127.0.0.1/" \
 | awk -F': ' '/^x-frame-options|^x-content-type-options|^referrer-policy|^permissions-policy|^content-security-policy/I' \
 | tee -a "$OUT"

say "\nStorage write check:"
tfile="storage/framework/health-$$.tmp"; echo ok > "$tfile" && rm -f "$tfile" && say "  storage/ writable: OK"

say "\nQueue + failures:"
if command -v supervisorctl >/dev/null 2>&1; then
  supervisorctl status swaeduae-queue 2>>"$OUT" | tee -a "$OUT" || echo "  supervisorctl not accessible (ok)" | tee -a "$OUT"
else
  echo "  supervisorctl not installed" | tee -a "$OUT"
fi
php artisan queue:failed --ansi | tee -a "$OUT" || true

say "\nErrors (last 50 lines of latest laravel log):"
latest_log=$(ls -1t storage/logs/laravel-*.log 2>/dev/null | head -n1)
[ -n "$latest_log" ] && tail -n 50 "$latest_log" | tee -a "$OUT" || echo "  (no laravel log yet)" | tee -a "$OUT"

say "\nMailer config:"
php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo "  SMTP host: ".(config("mail.mailers.smtp.host") ?: "(none)").PHP_EOL;' \
 | tee -a "$OUT" || true

say "\nDone -> $OUT"

# Success ping (healthchecks.io)
if [ -n "${HC_URL:-}" ]; then
  curl -fsS -m 10 -o /dev/null "$HC_URL" || true
fi
