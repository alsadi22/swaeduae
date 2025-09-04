#!/usr/bin/env bash
set -euo pipefail
cd /var/www/swaeduae || exit 0
URL="$(grep -m1 '^HEALTHCHECKS_URL=' .env 2>/dev/null | sed -E 's/^HEALTHCHECKS_URL=//')"
[ -n "${URL:-}" ] || exit 0
curl -fsS -m 10 -o /dev/null "$URL" || true

