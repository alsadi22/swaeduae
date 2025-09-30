#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -u
HOST="swaeduae.ae"
CF_BASE="https://swaeduae.ae"
ORIGIN_HTTP="http://127.0.0.1"
ORIGIN_HTTPS="https://$HOST"
OUT="public/health/fullcheck-$(date +'%Y-%m-%d_%H%M%S').txt"
exec > >(tee "$OUT") 2>&1

echo "=== FULL CHECK ($(date -u +"%F %T") UTC) ==="
echo "HOST=$HOST"

echo; echo "-- Artisan sanity --"
php -v | head -n1 || true
php -l routes/web.php || true

echo; echo "-- Cloudflare path --"
for p in / /opportunities /about /contact /privacy /terms /qr/verify; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I "$CF_BASE$p")
  printf "CF %-20s -> %s\n" "$p" "$code"
done
for p in /account/applications /account/certificates; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I "$CF_BASE$p")
  loc=$(curl -sD - -o /dev/null "$CF_BASE$p" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}')
  printf "CF %-20s -> %s (Location: %s)\n" "$p" "$code" "${loc:-'-'}"
done

echo; echo "-- Origin HTTP (port 80, with Host header) --"
for p in / /opportunities /about /contact /privacy /terms /qr/verify; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I -H "Host: $HOST" "$ORIGIN_HTTP$p")
  printf "ORIGIN80 %-16s -> %s\n" "$p" "$code"
done
for p in /account/applications /account/certificates; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -I -H "Host: $HOST" "$ORIGIN_HTTP$p")
  loc=$(curl -sD - -o /dev/null -H "Host: $HOST" "$ORIGIN_HTTP$p" | awk 'BEGIN{IGNORECASE=1}/^location:/{print $2}')
  printf "ORIGIN80 %-16s -> %s (Location: %s)\n" "$p" "$code" "${loc:-'-'}"
done

echo; echo "-- Origin HTTPS (port 443 via loopback, ignores self-signed) --"
for p in / /opportunities; do
  code=$(curl -k -s -o /dev/null -w "%{http_code}" -I --resolve "$HOST:443:127.0.0.1" "$ORIGIN_HTTPS$p")
  printf "ORIGIN443 %-16s -> %s\n" "$p" "$code"
done

echo; echo "-- nginx listen(443) --"
sudo ss -tlnp 2>/dev/null | grep ':443' || echo "NOT LISTENING on 443"

echo; echo "-- Latest Laravel log tail --"
latest=$(ls -1t storage/logs/laravel-*.log 2>/dev/null | head -n1 || true)
if [[ -n "${latest:-}" ]]; then echo "Log: $latest"; tail -n 40 "$latest"; else echo "No laravel log yet."; fi

echo; echo "Saved report: $OUT"
