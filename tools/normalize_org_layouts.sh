PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

# Find org blades that incorrectly extend layouts.app (either quotes)
FILES=$(grep -RIl --include='*.blade.php' -E "^@extends\(['\"]layouts\.app['\"]\)" resources/views/org || true)

if [ -z "${FILES}" ]; then
  echo "No org blades extend layouts.app — nothing to change."
  exit 0
fi

echo "Backing up & updating:"
for f in $FILES; do
  cp -a "$f" "$f.bak_$STAMP"
  # Replace only the first line/first match; keep whitespace intact
  sed -i -E "1s/^@extends\(['\"]layouts\.app['\"]\)/@extends('org.layout')/" "$f"
  echo "  ✓ $f  (backup: $f.bak_$STAMP)"
done

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "Done. Open /org pages and hard-refresh (Ctrl/Cmd+Shift+R)."
