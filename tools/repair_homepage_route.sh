#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"
F="routes/web.php"

# backup current file
cp -a "$F" "$F.recover_$STAMP"

# strip anything before the first <?php
awk 'BEGIN{seen=0} { if(!seen){ if($0 ~ /<\?php/){ seen=1; print; } } else { print } }' "$F" > "$F.__tmp__" && mv "$F.__tmp__" "$F"

# make sure file starts with <?php
grep -q '^<\?php' "$F" || sed -i '1s/^/<?php\n/' "$F"

# add fallback route once (inside PHP, right after the opener)
grep -q "home.fallback" "$F" || sed -i '1a Route::get("/", function(){ return view("public.home"); })->name("home.fallback");' "$F"

php artisan route:clear >/dev/null
php artisan route:cache >/dev/null

echo -n "Homepage bytes now: "
curl -sk https://swaeduae.ae/ | wc -c
