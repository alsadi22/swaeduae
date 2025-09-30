#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

CSS="public/css/brand.css"
mkdir -p "$(dirname "$CSS")"
[ -f "$CSS" ] || touch "$CSS"
cp -a "$CSS" "${CSS}.bak_$(date +%F_%H%M%S)"

# Remove any previous block and re-append (idempotent)
awk '
  /\/\* org-menu-fix:start \*\// {skip=1}
  !skip {print}
  /\/\* org-menu-fix:end \*\// {skip=0}
' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true

cat >> "$CSS" <<'CSS'
/* org-menu-fix:start */
/* Constrain the org console dropdown / panel so it doesn't overlap the page */
.navbar .dropdown-menu.org-menu,
.navbar .dropdown-menu#orgMenu,
.navbar .dropdown-menu[aria-labelledby="orgConsole"],
.navbar .dropdown-menu.dropdown-menu-end {
  right: .75rem !important;   /* dock to the right */
  left: auto !important;
  position: fixed !important; /* keep it steady while scrolling */
  top: 64px !important;       /* just below the navbar */
  width: min(92vw, 360px);    /* sensible width on all screens */
  max-height: calc(100vh - 88px);
  overflow: auto;
  border-radius: 14px;
  box-shadow: 0 24px 48px rgba(2,6,23,.24);
  z-index: 1055;              /* above navbar, below modals */
}

/* Subtle full-screen dimmer using a big box-shadow ring */
.navbar .dropdown-menu.org-menu.show,
.navbar .dropdown-menu#orgMenu.show,
.navbar .dropdown-menu[aria-labelledby="orgConsole"].show,
.navbar .dropdown-menu.dropdown-menu-end.show {
  box-shadow:
    0 24px 48px rgba(2,6,23,.24),
    0 0 0 9999px rgba(15,23,42,.35);
}

/* RTL: dock to the left edge instead */
[dir="rtl"] .navbar .dropdown-menu.org-menu,
[lang="ar"] .navbar .dropdown-menu.org-menu,
[dir="rtl"] .navbar .dropdown-menu#orgMenu,
[lang="ar"] .navbar .dropdown-menu#orgMenu,
[dir="rtl"] .navbar .dropdown-menu[aria-labelledby="orgConsole"],
[lang="ar"] .navbar .dropdown-menu[aria-labelledby="orgConsole"],
[dir="rtl"] .navbar .dropdown-menu.dropdown-menu-end,
[lang="ar"] .navbar .dropdown-menu.dropdown-menu-end {
  left: .75rem !important;
  right: auto !important;
}

/* Make sure generic navbar dropdowns don't stretch full width */
.navbar .dropdown-menu {
  width: auto;
  min-width: 16rem;
}
/* org-menu-fix:end */
CSS

echo "Rebuilding cached views (no downtime)…"
php artisan view:clear >/dev/null && php artisan view:cache >/dev/null || true

echo "✅ Org menu overlap fix applied to $CSS"
echo "Tip: hard-refresh the browser (Ctrl/Cmd+Shift+R)."
