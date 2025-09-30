#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

MENU="$(grep -RIl --include='*.blade.php' -n 'Organization Console' resources/views | head -n1 || true)"
if [ -z "$MENU" ]; then
  echo "❌ Could not find a Blade file containing 'Organization Console'.";
  exit 1
fi
echo "Target menu file: $MENU"
cp -a "$MENU" "$MENU.bak_$STAMP"

# 1) Ensure the first .dropdown-menu has an ID
if ! grep -q 'id="orgConsoleMenu"' "$MENU"; then
  sed -i -E '0,/(<div[^>]*class="[^"]*\bdropdown-menu\b[^"]*"[^>]*)(>)/s//\1 id="orgConsoleMenu"\2/' "$MENU"
  echo "→ Injected id=\"orgConsoleMenu\""
fi

# 2) Add/merge inline style with strong, safe rules
if grep -q 'id="orgConsoleMenu"[^>]*style=' "$MENU"; then
  sed -i -E 's/(id="orgConsoleMenu"[^>]*style=")/\1position:fixed !important; top:64px !important; right:.75rem !important; left:auto !important; width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important; z-index:1050 !important; border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24); /' "$MENU"
else
  sed -i -E 's/(id="orgConsoleMenu")/\1 style="position:fixed !important; top:64px !important; right:.75rem !important; left:auto !important; width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important; z-index:1050 !important; border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24);" /' "$MENU"
fi
echo "→ Applied inline layout rules"

# 3) Rebuild views
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Done. Backup at: $MENU.bak_$STAMP"
echo "Tip: hard-refresh (Ctrl/Cmd+Shift+R) on /org pages."
