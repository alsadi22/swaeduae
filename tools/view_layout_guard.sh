#!/usr/bin/env bash
set -euo pipefail
rc=0
while IFS= read -r -d '' f; do
  base="$(basename "$f")"
  dir="$(dirname "$f")"
  # skip partials/alt layouts/components
  [[ "$base" =~ ^_ ]] && continue
  [[ "$base" =~ ^layout.*\.blade\.php$ ]] && continue
  [[ "$base" == "rescue.blade.php" ]] && continue
  [[ "$dir" =~ /(partials|components)(/|$) ]] && continue
  if ! grep -qE "@extends\(['\"]public\.layout['\"]\)" "$f"; then
    echo "$f"; rc=1
  fi
done < <(find resources/views/public -type f -name '*.blade.php' -print0)

if (( rc )); then
  echo "FAIL: Public views not extending public.layout (listed above)"; exit 1
else
  echo "view_layout_guard: OK"; exit 0
fi
