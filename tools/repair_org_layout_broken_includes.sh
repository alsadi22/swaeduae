#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
LAY=resources/views/org/layout.blade.php
[ -f "$LAY" ] || { echo "Missing $LAY"; exit 1; }
cp "$LAY" "${LAY}.bak_$(date +%F_%H%M%S)"

# Replace orphaned argument lines with full @includeIf(...)
sed -Ei \
  -e "s/^[[:space:]]*\('org\.argon\._sidenav'\)[[:space:]]*$/@includeIf('org.argon._sidenav')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._navbar'\)[[:space:]]*$/@includeIf('admin.argon._navbar')/g" \
  -e "s/^[[:space:]]*\('org\.partials\.menu'\)[[:space:]]*$/@includeIf('org.partials.menu')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._footer'\)[[:space:]]*$/@includeIf('admin.argon._footer')/g" \
  "$LAY"

# Drop any blank, orphan @includeIf lines left behind
sed -Ei "/^[[:space:]]*@includeIf[[:space:]]*$/d" "$LAY"

echo "Patched $LAY"
