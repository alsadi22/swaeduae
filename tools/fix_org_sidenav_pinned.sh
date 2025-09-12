#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"
LAYOUT="resources/views/org/layout.blade.php"

[ -f "$LAYOUT" ] || { echo "❌ $LAYOUT not found"; exit 1; }

cp -a "$LAYOUT" "$LAYOUT.bak_$STAMP"

# 1) Ensure body has g-sidenav-pinned (once)
if ! grep -q 'g-sidenav-pinned' "$LAYOUT"; then
  sed -i -E 's/(<body[^>]*class="[^"]*\bg-sidenav-show\b)([^"]*")/\1 g-sidenav-pinned\2/' "$LAYOUT"
  echo "→ Added g-sidenav-pinned to <body>."
else
  echo "→ g-sidenav-pinned already present on <body>."
fi

# 2) Inject a tiny CSS tweak before </head> (idempotent via marker)
if ! grep -q 'org-sidenav-pin-fix:start' "$LAYOUT"; then
  awk '
    BEGIN{done=0}
    /<\/head>/ && !done {
      print "    <style>/* org-sidenav-pin-fix:start */"
      print "    /* Shift content so it does not sit under the pinned sidenav (desktop only) */"
      print "    @media (min-width: 992px){"
      print "      body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left: 280px; }"
      print "      [dir=\"rtl\"] body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0; margin-right:280px; }"
      print "    }"
      print "    /* org-sidenav-pin-fix:end */</style>"
      print
      print $0
      done=1; next
    }
    {print}
  ' "$LAYOUT" > "$LAYOUT.__tmp__" && mv "$LAYOUT.__tmp__" "$LAYOUT"
  echo "→ Injected org-sidenav-pin-fix CSS."
else
  echo "→ org-sidenav-pin-fix CSS already present."
fi

# 3) Rebuild views
php artisan optimize:clear >/dev/null || true
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Sidenav pinned fix applied to $LAYOUT"
echo "   Backup: $LAYOUT.bak_$STAMP"
echo "Tip: Hard-refresh the org dashboard (Ctrl/Cmd+Shift+R)."
