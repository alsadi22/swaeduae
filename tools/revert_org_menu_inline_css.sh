PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
MENU="$(grep -RIl --include='*.blade.php' -n 'Organization Console' resources/views | head -n1 || true)"
[ -z "$MENU" ] && { echo "Menu not found."; exit 1; }
LAST_BAK="$(ls -1t "$MENU".bak_* 2>/dev/null | head -n1 || true)"
[ -z "$LAST_BAK" ] && { echo "No backup found."; exit 1; }
cp -a "$LAST_BAK" "$MENU"
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ Reverted $MENU from $LAST_BAK"
