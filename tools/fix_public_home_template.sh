#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

VIEW="resources/views/public/home.blade.php"
CTRL="app/Http/Controllers/HomeController.php"

# Backups
cp -a "$VIEW" "$VIEW.bak_$STAMP"
cp -a "$CTRL" "$CTRL.bak_$STAMP"

# 1) Fix missing named route in the search form
sed -i "s@route('opportunities.index')@route('events.browse')@g" "$VIEW"

# 2) Make the view tolerant to either \$tiles or \$opps
#    a) inject a helper line once, right after the first @section('content')
if ! grep -q "@php(\$tiles" "$VIEW"; then
  awk '
    BEGIN{done=0}
    /@section\(.content./ && !done { print; print "  @php(\$tiles = \$tiles ?? (\$opps ?? collect()))"; done=1; next }
    { print }
  ' "$VIEW" > "$VIEW.__tmp__" && mv "$VIEW.__tmp__" "$VIEW"
fi
#    b) switch the loop to use $tiles
sed -i -E "s/@forelse\(\s*\$opps\s+as\s+\$o\)/@forelse(\$tiles as \$o)/" "$VIEW"

# 3) Make the controller pass both variables (tiles + opps) to public.home
#    We already build \$opps and \$tiles in your controller; just add params to the return.
#    Replace the return line to include both keys.
sed -i -E "s@return[[:space:]]+view\('public\.home',\s*\[@return view('public.home',[@" "$CTRL"
# add keys if missing
if ! grep -q "'opps' *=>" "$CTRL"; then
  sed -i -E "0,/@return view\('public\.home',\s*\[/s//return view('public.home',[\n              'opps' => \$opps,/" "$CTRL"
fi
if ! grep -q "'tiles' *=>" "$CTRL"; then
  sed -i -E "0,/@return view\('public\.home',\s*\[/s//return view('public.home',[\n              'tiles' => \$tiles,/" "$CTRL"
fi

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Patched public.home and HomeController (backups: $VIEW.bak_$STAMP , $CTRL.bak_$STAMP)"
