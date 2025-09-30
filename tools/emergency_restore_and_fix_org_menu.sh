#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

say(){ echo -e "$*"; }

say "== 1) Purge caches & compiled views =="
php artisan optimize:clear >/dev/null || true
rm -f storage/framework/views/* 2>/dev/null || true
rm -f bootstrap/cache/*.php 2>/dev/null || true

say "== 2) Remove any leftover org_menu_hotfix includes (if any) =="
grep -RIl "partials\.org_menu_hotfix" resources/views 2>/dev/null | while read -r f; do
  cp -a "$f" "$f.bak_$STAMP"
  sed -i '/partials\.org_menu_hotfix/d' "$f"
  echo "Cleaned include in $f"
done

say "== 3) Disable hotfix partial if it exists =="
if [ -f resources/views/partials/org_menu_hotfix.blade.php ]; then
  mv resources/views/partials/org_menu_hotfix.blade.php resources/views/partials/org_menu_hotfix.blade.php.disabled_"$STAMP"
  echo "Disabled partial: resources/views/partials/org_menu_hotfix.blade.php"
fi

say "== 4) Clear log so new errors are obvious =="
: > storage/logs/laravel.log 2>/dev/null || true

say "== 5) Rebuild view cache =="
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

say "== 6) Locate 'Organization Console' menu and apply a tiny inline style (no global CSS/JS) =="
MENU_FILE="$(grep -RIl --include='*.blade.php' -n 'Organization Console' resources/views | head -n1 || true)"
if [ -n "$MENU_FILE" ]; then
  cp -a "$MENU_FILE" "$MENU_FILE.bak_$STAMP"
  # Add ONE inline style to the first dropdown-menu container so it doesn't overlap the page
  sed -i -E '0,/(<div[^>]+class="[^"]*\bdropdown-menu\b[^"]*"[^>]*)(>)/s//\1 style="position:fixed; top:64px; right:.75rem; left:auto; width:min(92vw,360px); max-height:calc(100vh - 88px); overflow:auto; z-index:1050; border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24);" \2/' "$MENU_FILE" || true
  echo "Patched inline style in: $MENU_FILE"
else
  echo "Could not auto-find the menu partial. No inline patch applied."
fi

say "== 7) Final cache clear =="
php artisan optimize:clear >/dev/null || true

say "== 8) Any fresh errors? (last 120 lines) =="
tail -n 120 storage/logs/laravel.log 2>/dev/null || true

say "✅ Done. Hard-refresh your browser (Ctrl/Cmd+Shift+R)."
