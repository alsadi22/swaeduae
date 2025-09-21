#!/usr/bin/env bash
set -euo pipefail
BASE="https://www.swaeduae.ae"
PAGES=(/ /about /org/login /qr/verify /certificates/verify /stories /organizations)
ok=1
for u in "${PAGES[@]}"; do
  H=$(curl -sS -L "$BASE$u")
  C=$(curl -s -o /dev/null -w '%{http_code}' "$BASE$u")
  T=$(grep -c 'cdn.tailwindcss.com' <<<"$H")
  N=$(grep -cE '>About<|>Verify Certificate<|>Login<' <<<"$H")
  printf "%-22s -> %s  tailwind=%s nav=%s\n" "$u" "$C" "$T" "$N"
  if [[ "$C" != "200" || "$T" -lt 1 || "$N" -lt 1 ]]; then ok=0; fi
done

# Optional: verify prefill on /certificates/verify
TEST_CODE=TEST-XOPLGZQA
HTML="$(curl -sS -L "$BASE/certificates/verify/$TEST_CODE")"
PREFILL="$(grep -o 'name="code"[^>]*value="[^"]*"' <<<"$HTML" | head -n1 || true)"
echo "prefill: ${PREFILL:-NONE}"
if [[ -z "${PREFILL}" ]]; then ok=0; fi

if (( ok )); then
  echo "theme_smoke: OK"; exit 0
else
  echo "theme_smoke: FAIL"; exit 1
fi
