PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
LAY='resources/views/layouts/app.blade.php'
STAMP="$(date +%F_%H%M%S)"
cp -a "$LAY" "$LAY.bak_$STAMP"

# Insert navbar after <body...> once
grep -q "argon_front._navbar" "$LAY" || \
awk '
  BEGIN{done=0}
  /<body[^>]*>/ && !done { print; print "    @includeIf('\''argon_front._navbar'\'')"; done=1; next }
  { print }
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

# Insert footer before </body> once
grep -q "argon_front._footer" "$LAY" || \
awk '
  BEGIN{done=0}
  /<\/body>/ && !done { print "    @includeIf('\''argon_front._footer'\'')"; print; done=1; next }
  { print }
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "Now checking live HTML:"
echo -n "Nav tag: "; curl -sk https://swaeduae.ae/ | grep -m1 -o "<nav[^>]*>" || echo "MISSING"
echo -n "Footer tag: "; curl -sk https://swaeduae.ae/ | grep -m1 -o "<footer[^>]*>" || echo "MISSING"
echo "Backup: $LAY.bak_$STAMP"
