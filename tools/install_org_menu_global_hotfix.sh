PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

inject_into_layout(){
  local f="$1"
  [ -f "$f" ] || return 0

  # already installed?
  if grep -q "<!-- org-menu-global-hotfix:start -->" "$f"; then
    echo "Hotfix already present in $f"
    return 0
  fi

  cp -a "$f" "$f.bak_${STAMP}"
  awk '
    BEGIN{done=0}
    /<\/body>/ && !done {
      print "<!-- org-menu-global-hotfix:start -->"
      print "<style>"
      print ":root{ --org-navbar-h:64px; --org-panel-w:360px; }"
      print ".org-panel{position:fixed!important; top:var(--org-navbar-h)!important; right:.75rem!important; left:auto!important;"
      print "  width:min(92vw,var(--org-panel-w))!important; max-height:calc(100vh - (var(--org-navbar-h) + 24px))!important;"
      print "  overflow:auto!important; border-radius:14px!important; box-shadow:0 24px 48px rgba(2,6,23,.24)!important; z-index:1050!important;}"
      print "[dir=rtl] .org-panel,[lang=ar] .org-panel{ left:.75rem!important; right:auto!important; }"
      print ".org-panel-backdrop{position:fixed; inset:0; background:rgba(15,23,42,.35); z-index:1049;}"
      print "body.org-panel-open{overflow:hidden!important;}"
      print "/* optional push so content never sits under the panel */"
      print "body.org-panel-open main, body.org-panel-open .container-fluid, body.org-panel-open .main-content, body.org-panel-open #content{"
      print "  margin-right:calc(var(--org-panel-w) + 24px); transition:margin .2s;}"
      print "[dir=rtl] body.org-panel-open main, [lang=ar] body.org-panel-open main,"
      print "[dir=rtl] body.org-panel-open .container-fluid, [lang=ar] body.org-panel-open .container-fluid,"
      print "[dir=rtl] body.org-panel-open .main-content,   [lang=ar] body.org-panel-open .main-content,"
      print "[dir=rtl] body.org-panel-open #content,        [lang=ar] body.org-panel-open #content{"
      print "  margin-left:calc(var(--org-panel-w) + 24px); margin-right:0;}"
      print "</style>"
      print "<script>(function(){"
      print " if(window.__OrgMenuGlobalHotfix) return; window.__OrgMenuGlobalHotfix=true;"
      print " function isOrgMenu(menu){ try{ return (menu && (menu.textContent||'').toLowerCase().indexOf('organization console')!==-1); }catch(e){ return false; } }"
      print " function addBackdrop(){ if(document.querySelector('.org-panel-backdrop')) return;"
      print "   var bd=document.createElement('div'); bd.className='org-panel-backdrop';"
      print "   document.body.appendChild(bd); bd.addEventListener('click',function(){"
      print "     try{ var t=document.querySelector('[data-bs-toggle=\"dropdown\"].show,[data-bs-toggle=\"dropdown\"].dropdown-toggle[aria-expanded=\"true\"]');"
      print "          if(t && window.bootstrap){ var inst=window.bootstrap.Dropdown.getInstance(t); if(inst) inst.hide(); } }catch(_){ }"
      print "   });"
      print " }"
      print " document.addEventListener('shown.bs.dropdown',function(e){"
      print "   var menu=e && e.target ? e.target.querySelector('.dropdown-menu'):null; if(!menu || !isOrgMenu(menu)) return;"
      print "   menu.classList.add('org-panel'); document.body.classList.add('org-panel-open'); addBackdrop();"
      print " });"
      print " document.addEventListener('hide.bs.dropdown',function(e){"
      print "   var menu=e && e.target ? e.target.querySelector('.dropdown-menu'):null; if(!menu || !isOrgMenu(menu)) return;"
      print "   document.body.classList.remove('org-panel-open'); document.querySelectorAll('.org-panel-backdrop').forEach(function(n){n.remove();});"
      print " });"
      print "})();</script>"
      print "<!-- org-menu-global-hotfix:end -->"
      print
      print $0
      done=1; next
    }
    {print}
  ' "$f.bak_${STAMP}" > "$f"

  echo "Injected global hotfix into $f"
}

# Likely layouts used by org/admin views
for f in \
  resources/views/org/argon/app.blade.php \
  resources/views/admin/argon/app.blade.php \
  resources/views/layouts/app.blade.php \
  resources/views/layouts/main.blade.php \
  resources/views/app.blade.php
do
  inject_into_layout "$f"
done

# Clear + cache views (no downtime)
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "✅ Global hotfix installed. If needed, adjust --org-navbar-h in the injected <style>."
echo "Tip: Hard-refresh (Ctrl/Cmd+Shift+R) or open a private window."
