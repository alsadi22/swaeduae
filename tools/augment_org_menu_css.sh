#!/usr/bin/env bash
set -euo pipefail
LAYOUT="resources/views/org/layout.blade.php"
STAMP="$(date +%F_%H%M%S)"
cp -a "$LAYOUT" "$LAYOUT.bak_$STAMP"

# If our marker exists, append a broader rule right after it (idempotent append guard)
grep -q 'org-menu-minimal:start' "$LAYOUT" || { echo "Marker not found in $LAYOUT"; exit 0; }
grep -q 'org-menu-minimal-any' "$LAYOUT" && { echo "Already augmented."; exit 0; }

awk '
  BEGIN{printed=0}
  {print}
  /org-menu-minimal:start/ && !printed {
    print "      /* org-menu-minimal-any: broaden selector in case menu lacks -end/-right */"
    print "      .navbar .dropdown-menu.show{"
    print "        position: fixed !important; top:64px !important; right:.75rem !important; left:auto !important;"
    print "        width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important;"
    print "        border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24); z-index:1050; }"
    print "      [dir=\"rtl\"] .navbar .dropdown-menu.show{ left:.75rem !important; right:auto !important; }"
    printed=1
  }
' "$LAYOUT" > "$LAYOUT.__tmp__" && mv "$LAYOUT.__tmp__" "$LAYOUT"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "Augmented org dropdown CSS. Backup: $LAYOUT.bak_$STAMP"
