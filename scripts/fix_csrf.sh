#!/usr/bin/env bash
set -euo pipefail
files=(
  "resources/views/volunteer/settings/_tabs.blade.php"
  "resources/views/org/opps/edit.blade.php"
  "resources/views/org/opps/create.blade.php"
  "resources/views/layouts/partials/footer.blade.php"
)

for f in "${files[@]}"; do
  [[ -f "$f" ]] || { echo "Skip (missing): $f"; continue; }

  # Detect if there's a non-GET form missing @csrf
  if awk '
    BEGIN{IGNORECASE=1; missing=0; printing=0}
    /<form[[:space:]>]/ {printing=1; buf=$0 ORS}
    printing && !/<\/form>/ {buf=buf $0 ORS}
    printing && /<\/form>/ {
      isPost = (buf ~ /method=[\"\047]post[\"\047]/) || (buf ~ /@method\((\"|\047)(PUT|PATCH|DELETE)(\"|\047)\)/)
      hasCsrf = (buf ~ /@csrf/)
      if (isPost && !hasCsrf) missing=1
      printing=0; buf=""
    }
    END{ exit(missing?0:1) }
  ' "$f"; then
    cp -a "$f" "$f.bak.$(date +%Y%m%d-%H%M%S)"
    awk '
      BEGIN{IGNORECASE=1; printing=0}
      /<form[[:space:]>]/ {
        printing=1; buf=$0 ORS; next
      }
      printing && !/<\/form>/ { buf=buf $0 ORS; next }
      printing && /<\/form>/ {
        # Decide if this form needs @csrf
        isPost = (buf ~ /method=[\"\047]post[\"\047]/) || (buf ~ /@method\((\"|\047)(PUT|PATCH|DELETE)(\"|\047)\)/)
        hasCsrf = (buf ~ /@csrf/)
        if (isPost && !hasCsrf) {
          sub(/<form[^>]*>/, "&\n    @csrf", buf)
        }
        printf "%s", buf
        printing=0; buf=""; print; next
      }
      { print }
    ' "$f" > "$f.tmp" && mv "$f.tmp" "$f"
    echo "Fixed @csrf in: $f"
  else
    echo "OK (no change): $f"
  fi
done
