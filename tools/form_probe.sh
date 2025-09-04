#!/usr/bin/env bash
set -euo pipefail
BASE="https://swaeduae.ae"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

# 1) Get form to capture cookies + CSRF token
curl -s -D "$TMP/h" -c "$TMP/c" "$BASE/contact" -o "$TMP/body.html"
TOKEN=$(grep -oP 'name="_token"\s+value="[^"]+"' "$TMP/body.html" | sed -E 's/.*value="([^"]+)".*/\1/' | head -n1)

if [ -z "$TOKEN" ]; then
  echo "Could not extract CSRF token"; exit 2
fi

# 2) Post form
RESP=$(curl -s -i -b "$TMP/c" -c "$TMP/c" -H 'Content-Type: application/x-www-form-urlencoded' \
  -H "Referer: $BASE/contact" \
  --data-urlencode "_token=$TOKEN" \
  --data-urlencode "name=Probe User" \
  --data-urlencode "email=probe@example.com" \
  --data-urlencode "message=Hello from tools/form_probe.sh" \
  -X POST "$BASE/contact")

echo "$RESP" | egrep "HTTP/|Location:|Set-Cookie|X-MicroCache"
# Expect 302 back to /contact with flash
