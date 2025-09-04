#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"
say(){ echo -e "$*"; }

say "== A) Clean up any previous hotfix traces (layouts, partials, CSS) =="

# Remove any include we may have added earlier
for f in \
  resources/views/org/argon/app.blade.php \
  resources/views/admin/argon/app.blade.php \
  resources/views/layouts/app.blade.php \
  resources/views/layouts/main.blade.php \
  resources/views/app.blade.php
do
  [ -f "$f" ] || continue
  cp -a "$f" "$f.bak_$STAMP"
  sed -i.bak "/partials\.org_menu_hotfix/d" "$f" || true
done

# Disable the partial if it still exists
if [ -f resources/views/partials/org_menu_hotfix.blade.php ]; then
  mv resources/views/partials/org_menu_hotfix.blade.php resources/views/partials/org_menu_hotfix.blade.php.disabled_"$STAMP"
fi

# Strip any org-menu CSS blocks we previously appended to brand.css
CSS="public/css/brand.css"
if [ -f "$CSS" ]; then
  cp -a "$CSS" "$CSS.recover_$STAMP"
  awk '
    /\/\* org-menu-fix:start \*\// {skip=1}
    !skip {print}
    /\/\* org-menu-fix:end \*\// {skip=0}
  ' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
  mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true
fi

say "== B) Blow away caches so a bad compile can’t linger =="
php artisan optimize:clear || true
rm -f storage/framework/views/* 2>/dev/null || true
rm -f bootstrap/cache/*.php 2>/dev/null || true
: > storage/logs/laravel.log 2>/dev/null || true

say "== C) Add a tiny, org-only CSS block that matches Bootstrap-4 (.dropdown-menu-right) =="
ORG_LAYOUT="resources/views/org/argon/app.blade.php"
if [ -f "$ORG_LAYOUT" ]; then
  # Only inject once
  if ! grep -q "org-menu-minimal:start" "$ORG_LAYOUT"; then
    cp -a "$ORG_LAYOUT" "$ORG_LAYOUT.bak_$STAMP"
    awk '
      BEGIN{done=0}
      /<\/head>/ && !done {
        print "    <style>/* org-menu-minimal:start */"
        print "    /* Constrain the Organization Console dropdown (Argon/BS4 uses .dropdown-menu-right) */"
        print "    .navbar .dropdown-menu-right.show, .navbar .dropdown-menu-end.show{"
        print "      position:fixed !important; top:64px !important; right:.75rem !important; left:auto !important;"
        print "      width:min(92vw,360px) !important; max-height:calc(100vh - 88px) !important; overflow:auto !important;"
        print "      border-radius:14px; box-shadow:0 24px 48px rgba(2,6,23,.24); z-index:1050; }"
        print "    [dir=\"rtl\"] .navbar .dropdown-menu-right.show, [lang=\"ar\"] .navbar .dropdown-menu-right.show{"
        print "      left:.75rem !important; right:auto !important; }"
        print "    /* org-menu-minimal:end */</style>"
        print
        print $0
        done=1; next
      }
      {print}
    ' "$ORG_LAYOUT" > "$ORG_LAYOUT.__tmp__" && mv "$ORG_LAYOUT.__tmp__" "$ORG_LAYOUT"
    echo "Injected minimal CSS into $ORG_LAYOUT"
  else
    echo "Minimal CSS already present in $ORG_LAYOUT"
  fi
else
  echo "WARNING: $ORG_LAYOUT not found; skipping org-only CSS inject."
fi

say "== D) Rebuild views and show any compile errors clearly =="
if ! php artisan view:cache ; then
  echo "---- Laravel error log (last 120 lines) ----"
  tail -n 120 storage/logs/laravel.log || true
  exit 1
fi

say "== E) Final clear to ensure fresh runtime =="
php artisan route:clear >/dev/null || true
php artisan config:clear >/dev/null || true

say "✅ Done. Please hard-refresh the homepage and the org dashboard (Ctrl/Cmd+Shift+R)."
