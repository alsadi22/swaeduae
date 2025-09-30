PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
LAY="resources/views/org/layout.blade.php"
STAMP=$(date +%F_%H%M%S)
cp -a "$LAY" "$LAY.bak_$STAMP"

# 1) CSS: shift main-content while the right-aligned dropdown is open
grep -q 'org-console-shift:start' "$LAY" || awk '
  BEGIN{done=0}
  /<\/head>/ && !done {
    print "    <style>/* org-console-shift:start */"
    print "      .org-console-open .main-content{ transition: margin .2s ease; }"
    print "      body:not([dir=\"rtl\"]).org-console-open .main-content{ margin-right:360px; }"
    print "      [dir=\"rtl\"].org-console-open .main-content{ margin-left:360px; }"
    print "    /* org-console-shift:end */</style>"
    print; done=1; next
  }
  {print}
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

# 2) JS: toggle that class when a right-aligned dropdown is shown/hidden
grep -q 'org-console-shift JS' "$LAY" || awk '
  BEGIN{done=0}
  /<\/body>/ && !done {
    print "  <script>/* org-console-shift JS */"
    print "  (function(){"
    print "    function isRight(menu){"
    print "      return menu && (menu.classList.contains(\"dropdown-menu-end\") || menu.classList.contains(\"dropdown-menu-right\"));"
    print "    }"
    print "    document.addEventListener(\"shown.bs.dropdown\", function(ev){"
    print "      var menu = ev.target && ev.target.querySelector && ev.target.querySelector(\".dropdown-menu\");"
    print "      if(isRight(menu)){ document.body.classList.add(\"org-console-open\"); }"
    print "    });"
    print "    document.addEventListener(\"hidden.bs.dropdown\", function(ev){"
    print "      var menu = ev.target && ev.target.querySelector && ev.target.querySelector(\".dropdown-menu\");"
    print "      if(isRight(menu)){ document.body.classList.remove(\"org-console-open\"); }"
    print "    });"
    print "  })();"
    print "  </script>"
    print; done=1; next
  }
  {print}
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "✅ Added org-console shift (backup: $LAY.bak_$STAMP)"
