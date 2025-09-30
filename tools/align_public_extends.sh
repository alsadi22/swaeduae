#!/usr/bin/env bash
set -u
OUTDIR="_backups/align_public_extends.$(date +%Y%m%d_%H%M%S)"; mkdir -p "$OUTDIR"

# Build list from guard output
LIST="$(bash tools/view_layout_guard.sh 2>&1 | sed -n "s/^OUTLIER: \(resources\/views\/public\/.*\.blade\.php\).*/\1/p")"

# Fallback: grep if guard produced nothing (shouldn't happen)
if [ -z "$LIST" ]; then
  LIST="$(find resources/views/public -type f -name "*.blade.php" \
            ! -name "layout.blade.php" -print0 \
          | xargs -0 grep -L "@extends(['\"]public\.layout" | sed 's#^\./##')"
fi

echo "== Targets =="
echo "$LIST"

mkdir -p resources/views/_legacy/public

while IFS= read -r f; do
  [ -z "$f" ] && continue
  [ ! -f "$f" ] && continue
  b="$(basename "$f")"

  # Skip partials + the canonical layout
  [[ "$b" == _* ]] && { echo "skip (partial): $f"; continue; }
  [[ "$b" == "layout.blade.php" ]] && { echo "skip (layout): $f"; continue; }

  # Quarantine old Argon layout instead of editing it
  if [[ "$b" =~ ^layout-.*\.blade\.php$ ]]; then
    tgt="resources/views/_legacy/public/$b"
    cp -a "$f" "$OUTDIR/${b}.bak"
    (git mv "$f" "$tgt" 2>/dev/null || mv "$f" "$tgt")
    echo "quarantined: $f -> $tgt"
    continue
  fi

  # Replace FIRST @extends(...) with @extends('public.layout')
  cp -a "$f" "$OUTDIR/$(basename "$f").bak"
  awk 'BEGIN{done=0}
       {
         if(!done && $0 ~ /@extends\(/){
           sub(/@extends\([^)]*\)/,"@extends('\''public.layout'\'')"); done=1
         }
         print
       }' "$f" > "$f.__new" && mv "$f.__new" "$f"
  echo "fixed: $f"
done <<< "$LIST"

echo "== Rebuilding caches =="
php artisan view:clear >/dev/null 2>&1 || true
php artisan route:clear >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true
php artisan route:cache >/dev/null 2>&1 || true

echo "== Guard re-run =="
bash tools/view_layout_guard.sh || true
