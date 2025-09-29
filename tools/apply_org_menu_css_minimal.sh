#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

ORG_LAYOUT="resources/views/org/argon/app.blade.php"
[ -f "$ORG_LAYOUT" ] || { echo "Missing $ORG_LAYOUT"; exit 0; }

cp -a "$ORG_LAYOUT" "$ORG_LAYOUT.bak_${STAMP}"

# Insert a tiny <style> block before </head> (idempotent)
grep -q "/* org-menu-minimal:start */" "$ORG_LAYOUT" || awk '
  BEGIN{done=0}
  /<\/head>/ && !done {
    print "    <style>/* org-menu-minimal:start */"
    print "    .navbar .dropdown-menu.dropdown-menu-end{ min-width: 18rem; }"
    print "    .navbar .dropdown-menu.dropdown-menu-end.show{"
    print "      position: fixed !important;"
    print "      top: 64px !important; right: .75rem !important; left: auto !important;"
    print "      width: min(92vw, 360px) !important; max-height: calc(100vh - 88px) !important; overflow: auto !important;"
    print "      border-radius: 14px; box-shadow: 0 24px 48px rgba(2,6,23,.24); z-index: 1050; }"
    print "    [dir=\"rtl\"] .navbar .dropdown-menu.dropdown-menu-end.show{ left:.75rem !important; right:auto !important; }"
    print "    /* org-menu-minimal:end */</style>"
    print
    print $0
    done=1; next
  }
  {print}
' "$ORG_LAYOUT" > "$ORG_LAYOUT.__tmp__" && mv "$ORG_LAYOUT.__tmp__" "$ORG_LAYOUT"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Minimal org menu CSS injected in $ORG_LAYOUT"
echo "Tip: Hard-refresh on /org pages."
