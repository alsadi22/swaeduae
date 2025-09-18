#!/usr/bin/env bash
set -eo pipefail
DOMAIN="${1:-swaeduae.ae}"
SUBS=( "@" "www" "admin" )

have(){ command -v "$1" >/dev/null 2>&1; }
have dig || { echo "dig not found (install: apt-get install -y dnsutils)"; exit 1; }
have curl || { echo "curl not found"; exit 1; }
have openssl || { echo "openssl not found"; exit 1; }

echo "== NS for $DOMAIN =="
dig +short NS "$DOMAIN" || true
echo

for sub in "${SUBS[@]}"; do
  host=$([ "$sub" = "@" ] && echo "$DOMAIN" || echo "$sub.$DOMAIN")
  echo "---- $host ----"
  echo "-- A/AAAA --"
  dig +short A    "$host" @1.1.1.1 || true
  dig +short AAAA "$host" @1.1.1.1 || true
  ip=$(dig +short A "$host" @1.1.1.1 | head -n1 || true)
  if [ -n "$ip" ]; then
    echo -n "rDNS: "
    dig +short -x "$ip" @1.1.1.1 || true
  fi
  echo "-- HTTPS headers (edge) --"
  curl -sSI "https://$host" | egrep -i 'HTTP/|server:|cf-|cache|age:' || true
  echo
done

echo "== HTTP from origin (loopback, Host header) =="
for sub in "${SUBS[@]}"; do
  host=$([ "$sub" = "@" ] && echo "$DOMAIN" || echo "$sub.$DOMAIN")
  printf "%-24s -> " "$host"
  curl -s -o /dev/null -w "%{http_code}\n" -H "Host: $host" http://127.0.0.1/
done
echo

echo "== TLS cert issuers (edge) =="
for sub in "${SUBS[@]}"; do
  host=$([ "$sub" = "@" ] && echo "$DOMAIN" || echo "$sub.$DOMAIN")
  echo "$host"
  timeout 8 openssl s_client -connect "$host:443" -servername "$host" -showcerts < /dev/null 2>/dev/null | \
    openssl x509 -noout -issuer -dates || true
  echo
done
