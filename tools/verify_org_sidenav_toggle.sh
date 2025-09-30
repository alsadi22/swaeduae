#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
LAY="resources/views/org/layout.blade.php"
NAV="resources/views/admin/argon/_navbar.blade.php"

echo "[Body tag]"
grep -n '<body' "$LAY"

echo "[Has main-content container?]"
grep -n 'class="main-content' "$LAY" | head -n2 || true

echo "[Navbar has toggle button?]"
grep -n 'id="org-sidenav-toggle"' "$NAV" || echo "(toggle button not found)"

echo "[Client script injected?]"
grep -n 'org_sidenav_pinned' "$LAY" || echo "(toggle script not found)"

echo "[CSS margins rules?]"
grep -n 'g-sidenav-pinned .main-content' "$LAY" || echo "(margin rules not found)"

echo "[Dropdown scoped CSS (prevents overlap)]"
awk '/org-menu-minimal:start/,/org-menu-minimal:end/' "$LAY"
