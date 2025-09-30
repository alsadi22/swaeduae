#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

# 1) Guards: no CDN/Bootstrap/Argon in public
! grep -RIn --exclude='*.bak*' -E 'cdn\.tailwindcss\.com|bootstrap(\.min)?\.(css|js)|argon(\.min)?\.(css|js)' resources/views/public || {
  echo "Guard fail: found forbidden assets in public"; exit 1; }

# 2) Public views extend layout (excluding allowed files)
./tools/view_layout_guard.sh

# 3) Tailwind builds without error (no output needed here)
npx --yes tailwindcss@3.4.10 -c tailwind.config.js -i resources/css/app.css -o /dev/null --minify

# 4) Laravel sanity (no DB I/O): route list
php artisan route:list >/dev/null

echo "theme_ci: OK"
