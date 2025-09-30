PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
LAY='resources/views/public/layout.blade.php'
STAMP="$(date +%F_%H%M%S)"
cp -a "$LAY" "$LAY.bak_$STAMP"

# Replace route('opportunities.index') with route('events.browse') if present
if grep -q "route('opportunities.index')" "$LAY"; then
  sed -i "s@route('opportunities.index')@route('events.browse')@g" "$LAY"
  echo "Updated Opportunities link to route('events.browse')."
else
  echo "Opportunities link using route('opportunities.index') not found; no change."
fi

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "Backup: $LAY.bak_$STAMP"
