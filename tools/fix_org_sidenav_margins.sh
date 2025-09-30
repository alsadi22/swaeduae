PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
LAY="resources/views/org/layout.blade.php"
STAMP=$(date +%F_%H%M%S)
cp -a "$LAY" "$LAY.bak_$STAMP"

# Add a style block before </head> if not present
grep -q 'org-sidenav-margins:start' "$LAY" || awk '
  BEGIN{done=0}
  /<\/head>/ && !done {
    print "    <style>/* org-sidenav-margins:start */"
    print "      body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:280px; }"
    print "      [dir=\\"rtl\\"] body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0; margin-right:280px; }"
    print "      @media (max-width: 991.98px){"
    print "        body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0 !important; margin-right:0 !important; }"
    print "      }"
    print "    /* org-sidenav-margins:end */</style>"
    print; done=1; next
  }
  {print}
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ margins style ensured (backup: $LAY.bak_$STAMP)"
