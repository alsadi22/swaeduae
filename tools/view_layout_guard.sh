#!/usr/bin/env bash
set -euo pipefail
OUT=0
while IFS= read -r -d '' f; do
  if grep -qE "@extends\(['\"]public\.layout['\"]\)" "$f"; then :; else
    echo "OUTLIER: $f"; OUT=1
  fi
done < <(find resources/views/public -type f -name "*.blade.php" -print0)
[ $OUT -eq 0 ] && echo "view_layout_guard: OK" || { echo "view_layout_guard: FAIL"; exit 1; }
