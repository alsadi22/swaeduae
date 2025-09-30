#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

echo "== 1) Locate the view that renders 'Organization Console' =="
CANDIDATES=$(grep -RIl --include='*.blade.php' -n 'Organization Console' resources/views || true)
if [ -z "${CANDIDATES}" ]; then
  echo "❌ Could not find any Blade file containing 'Organization Console'."
  exit 1
fi
echo "$CANDIDATES" | nl -ba

MENU_FILE="$(echo "$CANDIDATES" | head -n1)"
echo "→ Using: $MENU_FILE"
cp -a "$MENU_FILE" "$MENU_FILE.bak_$STAMP"

echo "== 2) Show a small context snippet (for sanity) =="
LC=$(grep -n -m1 'Organization Console' "$MENU_FILE" | cut -d: -f1 || echo 1)
START=$((LC>20?LC-20:1)); END=$((LC+30))
nl -ba "$MENU_FILE" | sed -n "${START},${END}p"

echo "== 3) Ensure first .dropdown-menu has id=orgConsoleMenu (once) =="
if ! grep -q 'id="orgConsoleMenu"' "$MENU_FILE"; then
  sed -i -E '0,/(<div[^>]*class="[^"]*\bdropdown-menu\b[^"]*"[^>]*)(>)/s//\1 id="orgConsoleMenu"\2/' "$MENU_FILE"
  echo "→ Injected id=\"orgConsoleMenu\""
else
  echo "→ id already present"
fi

echo "== 4) Apply inline layout rules (strong, safe) =="
if grep -q 'id="orgConsoleMenu"[^>]*style=' "$MENU_FILE"; then
  # Prepend our rules so they win
  sed -i -E 's/(id="orgConsoleMenu"[^>]*style=")/\1position:fixed !important; top:64px !important; right:.75rem !important; left:auto !important; width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important; z-index:1050 !important; border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24); /' "$MENU_FILE"
  echo "→ Merged into existing style"
else
  sed -i -E 's/(id="orgConsoleMenu")/\1 style="position:fixed !important; top:64px !important; right:.75rem !important; left:auto !important; width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important; z-index:1050 !important; border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24);" /' "$MENU_FILE"
  echo "→ Added new inline style"
fi

echo "== 5) Clear & cache views =="
php artisan optimize:clear >/dev/null || true
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "== 6) Done. Backup at $MENU_FILE.bak_$STAMP"
echo "Tip: Hard-refresh the org dashboard (Ctrl/Cmd+Shift+R)."
