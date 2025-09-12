#!/usr/bin/env bash
set -euo pipefail
file="resources/views/admin/dashboard.blade.php"
[ -f "$file" ] || { echo "No $file"; exit 0; }
echo "Variables referenced in $file:"
grep -oE '(\$[A-Za-z_][A-Za-z0-9_]*?)' "$file" | sed 's/[),.]$//' | sort -u
