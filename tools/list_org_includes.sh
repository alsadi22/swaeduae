#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
main="resources/views/org/dashboard.blade.php"
echo "Main: $main"
out="_org_files.$$.txt"
printf "%s\n" "$main" > "$out"
grep -oE "@include\(['\"][^)]+['\"]\)" "$main" | sed -E "s/.*\(['\"]([^'\"]+)['\"].*/\1/" | while read -r inc; do
  f="resources/views/${inc//./\/}.blade.php"; [ -f "$f" ] && printf "%s\n" "$f" >> "$out"
done
[ -f resources/views/org/partials/dashboard_v1.blade.php ] && printf "%s\n" "resources/views/org/partials/dashboard_v1.blade.php" >> "$out"
nl -ba "$out"
rm -f "$out"
