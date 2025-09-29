#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"
NAV="resources/views/admin/argon/_navbar.blade.php"
ORG_LAYOUT="resources/views/org/layout.blade.php"
BRAND="public/css/brand.css"
mkdir -p public/css public/js

echo "== 1) Ensure brand.css has scoped org dropdown/sidenav rules =="
touch "$BRAND"
cp -a "$BRAND" "$BRAND.bak_$STAMP"
awk '/\/\* org-ui:start \*\//{skip=1} !skip{print} /\/\* org-ui:end \*\//{skip=0}' "$BRAND" > "$BRAND.__tmp__" || true
mv "$BRAND.__tmp__" "$BRAND" 2>/dev/null || true
cat >> "$BRAND" <<'CSS'
/* org-ui:start */
body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:280px; }
[dir="rtl"] body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0; margin-right:280px; }

.navbar .dropdown-menu-end.show, .navbar .dropdown-menu-right.show{
  position: fixed !important;
  top: 64px !important; right: .75rem !important; left: auto !important;
  width: min(92vw, 360px) !important;
  max-height: calc(100vh - 96px) !important;
  overflow: auto !important; border-radius: 14px;
  box-shadow: 0 24px 48px rgba(2,6,23,.24); z-index: 1050;
  transform: none !important;
}
[dir="rtl"] .navbar .dropdown-menu-end.show, [lang="ar"] .navbar .dropdown-menu-end.show{
  left: .75rem !important; right: auto !important;
}
/* org-ui:end */
CSS
echo "   - brand.css updated (backup: $BRAND.bak_$STAMP)"

echo "== 2) Make bare dropdown menus right-aligned in Argon navbar =="
if [ -f "$NAV" ]; then
  cp -a "$NAV" "$NAV.bak_$STAMP"
  perl -0777 -pe 's/class="dropdown-menu(?![^"]*(dropdown-menu-(end|right)))/class="dropdown-menu dropdown-menu-end/g' -i "$NAV"
  echo "   - ensured dropdown-menu-end in $NAV (backup: $NAV.bak_$STAMP)"
else
  echo "   - $NAV not found; skipping"
fi

echo "== 3) Ensure org UI JS is loaded exactly once (toggle + open-state) =="
ORGJS="public/js/org-ui.js"
cp -a "$ORGJS" "$ORGJS.bak_$STAMP" 2>/dev/null || true
cat > "$ORGJS" <<'JS'
(function(){
  try{
    var KEY='org_sidenav_pinned';
    function setPinned(p){
      document.body.classList.toggle('g-sidenav-pinned', !!p);
      try{ localStorage.setItem(KEY, p?'1':'0'); }catch(e){}
    }
    try{ setPinned(localStorage.getItem(KEY)!=='0'); }catch(e){}
    document.addEventListener('click', function(ev){
      var btn = ev.target.closest && ev.target.closest('#org-sidenav-toggle');
      if(!btn) return;
      ev.preventDefault();
      setPinned(!document.body.classList.contains('g-sidenav-pinned'));
    });

    function onOpen(){ document.body.classList.add('org-console-open'); }
    function onClose(){ document.body.classList.remove('org-console-open'); }

    if (window.bootstrap){
      document.addEventListener('shown.bs.dropdown', function(e){ if(e.target.closest('.navbar')) onOpen(); });
      document.addEventListener('hide.bs.dropdown',  function(e){ if(e.target.closest('.navbar')) onClose(); });
    } else {
      var mo = new MutationObserver(function(){
        var anyOpen = !!document.querySelector('.navbar .dropdown-menu.show');
        if (anyOpen) onOpen(); else onClose();
      });
      mo.observe(document.documentElement, {subtree:true, attributes:true, attributeFilter:['class']});
    }
  }catch(e){}
})();
JS

if [ -f "$ORG_LAYOUT" ]; then
  cp -a "$ORG_LAYOUT" "$ORG_LAYOUT.bak_$STAMP"
  grep -q 'js/org-ui.js' "$ORG_LAYOUT" || awk '
    /<\/body>/ && !done { print "  <script src=\"{{ asset('\''js/org-ui.js'\'') }}\" defer></script>"; print; done=1; next }
    {print}
  ' "$ORG_LAYOUT" > "$ORG_LAYOUT.__tmp__" && mv "$ORG_LAYOUT.__tmp__" "$ORG_LAYOUT"
  echo "   - ensured org-ui.js is loaded in org layout (backup: $ORG_LAYOUT.bak_$STAMP)"
fi

echo "== 4) Rebuild caches =="
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "Done."
