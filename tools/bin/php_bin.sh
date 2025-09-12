#!/usr/bin/env bash
set -euo pipefail
# Prefer PHP-CLI; reject php-cgi (Server API != CLI)
cands=(/usr/bin/ea-php84 /opt/cpanel/ea-php84/root/usr/bin/php /usr/local/bin/ea-php84 /usr/local/bin/php /usr/bin/php php)
PHP_BIN=""
for c in "${cands[@]}"; do
  if command -v "$c" >/dev/null 2>&1; then
    sapi="$("$c" -i 2>/dev/null | awk -F'=> ' '/^Server API/ {print $2}')"
    if printf '%s' "$sapi" | grep -qi 'cli'; then PHP_BIN="$c"; break; fi
  fi
done
# Last resort: whatever "php" is
if [ -z "${PHP_BIN:-}" ]; then PHP_BIN="$(command -v php)"; fi
exec "$PHP_BIN" "$@"
