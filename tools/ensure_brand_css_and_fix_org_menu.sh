#!/usr/bin/env bash
set -euo pipefail

CSS="public/css/brand.css"
PARTIAL="resources/views/partials/brand_css.blade.php"
STAMP="$(date +%F_%H%M%S)"

mkdir -p "$(dirname "$CSS")" "$(dirname "$PARTIAL")"
[ -f "$CSS" ] || touch "$CSS"

# 1) Create a tiny partial to always load brand.css with cache-busting
if [ ! -f "$PARTIAL" ]; then
  cat > "$PARTIAL" <<'BLADE'
<link rel="stylesheet"
      href="{{ asset('css/brand.css') }}?v={{ @filemtime(public_path('css/brand.css')) }}"
      as="style" />
BLADE
fi

# 2) Inject that partial into common layout files before </head> (idempotent)
insert_include() {
  local file="$1"
  [ -f "$file" ] || return 0
  grep -q "partials\.brand_css" "$file" && return 0
  cp -a "$file" "$file.bak_$STAMP"
  awk '
    BEGIN{done=0}
    /<\/head>/ && !done {
      print "    @includeIf('\''partials.brand_css'\'')"
      print
      done=1
      next
    }
    {print}
  ' "$file.bak_$STAMP" > "$file"
  echo "Injected brand_css into $file"
}

# Candidate layouts (exists-check inside loop):
for f in \
  resources/views/layouts/app.blade.php \
  resources/views/layouts/guest.blade.php \
  resources/views/layouts/argon.blade.php \
  resources/views/admin/argon/app.blade.php \
  resources/views/org/argon/app.blade.php \
  resources/views/org/layouts/app.blade.php \
  resources/views/layouts/main.blade.php \
  resources/views/app.blade.php
do
  insert_include "$f"
done

# 3) Ensure our org-menu CSS is present (replace our block if already there)
cp -a "$CSS" "$CSS.bak_$STAMP" || true
awk '
  /\/\* org-menu-fix:start \*\// {skip=1}
  !skip {print}
  /\/\* org-menu-fix:end \*\// {skip=0}
' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true

cat >> "$CSS" <<'CSS'
/* org-menu-fix:start */
:root{ --navbar-height:64px; }
/* Lock scroll when a navbar dropdown is open */
body:has(.navbar .dropdown-menu.show){ overflow:hidden; }
/* Viewport backdrop */
.navbar:has(.dropdown-menu.show)::after{
  content:""; position:fixed; inset:0; background:rgba(15,23,42,.35);
  z-index:1049; pointer-events:none;
}
/* Make any right-aligned dropdown a fixed, narrow panel */
.dropdown-menu-end.show{
  position:fixed !important;
  top: var(--navbar-height) !important;
  right:.75rem !important; left:auto !important;
  width:min(92vw, 360px);
  max-height:calc(100vh - (var(--navbar-height) + 24px));
  overflow:auto; border-radius:14px;
  box-shadow:0 24px 48px rgba(2,6,23,.24);
  z-index:1050;
}
/* If yours lives under .navbar, this also catches it. */
.navbar .dropdown-menu.show{
  position:fixed !important;
  top: var(--navbar-height) !important;
}
/* RTL */
[dir="rtl"] .dropdown-menu-end.show,
[lang="ar"] .dropdown-menu-end.show{
  left:.75rem !important; right:auto !important;
}
/* Avoid huge generic dropdowns */
.navbar .dropdown-menu{ width:auto; min-width:16rem; }
/* org-menu-fix:end */
CSS

# 4) Rebuild caches (no downtime)
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

# 5) Quick verification: list any layouts that now include our partial
echo "----- VERIFY INCLUDE -----"
grep -RIn --color=never "partials\.brand_css" resources/views || true

echo "----- VERIFY CSS MARKER -----"
grep -n "org-menu-fix:start" "$CSS" || true

echo "✅ Done. Hard-refresh your browser (Ctrl/Cmd+Shift+R)."
