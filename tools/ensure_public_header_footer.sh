PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
LAY='resources/views/public/layout.blade.php'
STAMP="$(date +%F_%H%M%S)"
[ -f "$LAY" ] || { echo "❌ $LAY not found"; exit 1; }

# Candidate partials to try (in order)
headers=(public/_navbar public/navbar public/_header public/header public/partials/_navbar public/partials/navbar public/partials/_header public/partials/header)
footers=(public/_footer public/footer public/partials/_footer public/partials/footer)

# Pick first existing header/footer
pick_part() {
  for p in "$@"; do
    f="resources/views/${p}.blade.php"
    [ -f "$f" ] && { echo "$p"; return; }
  done
  echo ""
}

H=$(pick_part "${headers[@]}")
F=$(pick_part "${footers[@]}")

echo "Header partial: ${H:-<none found>}"
echo "Footer partial: ${F:-<none found>}"

cp -a "$LAY" "$LAY.bak_$STAMP"

# Insert header include before the first @yield('content')
if [ -n "$H" ] && ! grep -q "public-header:auto" "$LAY"; then
  awk -v inc="@includeIf('\''$H'\'')" '
    BEGIN{done=0}
    /@yield\(\x27content\x27\)/ && !done {
      print "    {{-- public-header:auto --}} " inc
      print
      done=1; next
    }
    {print}
  ' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"
fi

# Insert footer include before </body>
if [ -n "$F" ] && ! grep -q "public-footer:auto" "$LAY"; then
  awk -v inc="@includeIf('\''$F'\'')" '
    BEGIN{done=0}
    /<\/body>/ && !done {
      print "    {{-- public-footer:auto --}} " inc
      print
      done=1; next
    }
    {print}
  ' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"
fi

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ Updated $LAY (backup: $LAY.bak_$STAMP)"
