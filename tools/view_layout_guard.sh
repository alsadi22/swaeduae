#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
OUTLIERS=()

while IFS= read -r f; do
  # Skip layout itself, components, and underscore-partials
  case "$f" in
    */resources/views/public/layout.blade.php) continue ;;
    */resources/views/public/components/*) continue ;;
    */resources/views/public/_*.blade.php) continue ;;
  esac
  if ! grep -qE "@extends\(['\"]public\.layout" "$f"; then
    OUTLIERS+=("${f#"$ROOT"/}")
  fi
done < <(find resources/views/public -type f -name "*.blade.php" ! -name "*.bak*")

if ((${#OUTLIERS[@]})); then
  printf "Outliers (must extend public.layout):\n"
  printf ' - %s\n' "${OUTLIERS[@]}"
  exit 1
fi
echo "view_layout_guard: OK"
