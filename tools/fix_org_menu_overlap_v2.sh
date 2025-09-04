#!/usr/bin/env bash
set -euo pipefail

CSS="public/css/brand.css"
mkdir -p "$(dirname "$CSS")"
[ -f "$CSS" ] || touch "$CSS"
cp -a "$CSS" "${CSS}.bak_$(date +%F_%H%M%S)"

# Remove previous org-menu-fix block (if any) and re-append improved rules
awk '
  /\/\* org-menu-fix:start \*\// {skip=1}
  !skip {print}
  /\/\* org-menu-fix:end \*\// {skip=0}
' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true

cat >> "$CSS" <<'CSS'
/* org-menu-fix:start */
:root{ --navbar-height:64px; }

/* Viewport-level backdrop when ANY navbar dropdown is open. Uses :has(), widely supported in modern browsers. */
body:has(.navbar .dropdown-menu.show){
  overflow:hidden; /* prevent page scroll behind the panel */
}
.navbar:has(.dropdown-menu.show)::after{
  content:"";
  position:fixed; inset:0;
  background:rgba(15,23,42,.35);
  z-index:1049; /* below the panel; above page */
  pointer-events:none; /* allow outside click to propagate and close the menu */
}

/* Force the org console dropdown to be a tidy, fixed panel docked to the edge */
.navbar .dropdown-menu.show{
  position:fixed !important;
  top: var(--navbar-height) !important;
  right:.75rem !important; left:auto !important;
  width:min(92vw, 360px);
  max-height:calc(100vh - (var(--navbar-height) + 24px));
  overflow:auto;
  border-radius:14px;
  box-shadow:0 24px 48px rgba(2,6,23,.24);
  z-index:1050; /* above the backdrop */
}

/* RTL: dock to the left */
[dir="rtl"] .navbar .dropdown-menu.show,
[lang="ar"] .navbar .dropdown-menu.show{
  left:.75rem !important; right:auto !important;
}

/* Ensure generic navbar dropdowns don't accidentally stretch full-width */
.navbar .dropdown-menu{ width:auto; min-width:16rem; }
/* org-menu-fix:end */
CSS

echo "Rebuilding cached views (no downtime)…"
php artisan view:clear >/dev/null && php artisan view:cache >/dev/null || true

echo "✅ Org menu overlap v2 applied to $CSS"
echo "Tip: hard-refresh (Ctrl/Cmd+Shift+R). If your navbar is taller, tweak --navbar-height."
