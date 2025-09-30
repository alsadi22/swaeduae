#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
LAY="resources/views/org/layout.blade.php"
STAMP=$(date +%F_%H%M%S)
cp -a "$LAY" "$LAY.bak_$STAMP"

awk '
BEGIN{inside=0}
 /\/\* org-menu-minimal:start \*\// {
   print "    <style>/* org-menu-minimal:start */";
   print "      /* Scoped: only right-aligned org navbar dropdowns */";
   print "      .navbar .dropdown-menu-end.show, .navbar .dropdown-menu-right.show{";
   print "        position: fixed !important;";
   print "        top: 72px !important; right: .75rem !important; left: auto !important;";
   print "        width: min(92vw, 360px) !important;";
   print "        max-height: calc(100vh - 96px) !important;";
   print "        overflow: auto !important; border-radius: 14px;";
   print "        box-shadow: 0 24px 48px rgba(2,6,23,.24); z-index: 1050;";
   print "        transform: none !important; }";
   print "      [dir=\\\"rtl\\\"] .navbar .dropdown-menu-end.show, [lang=\\\"ar\\\"] .navbar .dropdown-menu-end.show{";
   print "        left: .75rem !important; right: auto !important; }";
   print "    /* org-menu-minimal:end */</style>";
   inside=1; next
 }
 /\/\* org-menu-minimal:end \*\// { inside=0; next }
 inside==1 { next }
 { print }
' "$LAY" > "$LAY.__tmp__" && mv "$LAY.__tmp__" "$LAY"

php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true
echo "Updated dropdown CSS (backup: $LAY.bak_$STAMP)."
