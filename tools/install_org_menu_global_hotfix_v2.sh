#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail
STAMP="$(date +%F_%H%M%S)"

PARTIAL="resources/views/partials/org_menu_hotfix.blade.php"
mkdir -p "$(dirname "$PARTIAL")"

# 1) Write the hotfix partial (inline CSS+JS). Idempotent: we overwrite the same file.
cat > "$PARTIAL" <<'BLADE'
<!-- org-menu-global-hotfix:start -->
<style>
:root{ --org-navbar-h:64px; --org-panel-w:360px; }

/* Fixed, narrow panel */
.org-panel{
  position:fixed !important;
  top:var(--org-navbar-h) !important;
  right:.75rem !important; left:auto !important;
  width:min(92vw,var(--org-panel-w)) !important;
  max-height:calc(100vh - (var(--org-navbar-h) + 24px)) !important;
  overflow:auto !important;
  border-radius:14px !important;
  box-shadow:0 24px 48px rgba(2,6,23,.24) !important;
  z-index:1050 !important;
}
[dir="rtl"] .org-panel,[lang="ar"] .org-panel{ left:.75rem !important; right:auto !important; }

/* Backdrop + body lock while open */
.org-panel-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.35); z-index:1049; }
body.org-panel-open{ overflow:hidden !important; }
</style>

<script>
(function(){
  if (window.__OrgMenuGlobalHotfix) return;
  window.__OrgMenuGlobalHotfix = true;

  function isOrgMenu(menu){
    try { return !!(menu && (menu.textContent || "").toLowerCase().includes("organization console")); }
    catch(e){ return false; }
  }
  function addBackdrop(){
    if (document.querySelector(".org-panel-backdrop")) return;
    var bd = document.createElement("div");
    bd.className = "org-panel-backdrop";
    document.body.appendChild(bd);
    bd.addEventListener("click", function(){
      try{
        var t = document.querySelector('[data-bs-toggle="dropdown"][aria-expanded="true"]');
        if (t && window.bootstrap){
          var inst = window.bootstrap.Dropdown.getInstance(t);
          if (inst) inst.hide();
        }
      }catch(_){}
    });
  }

  // Bootstrap events (works with Argon/Bootstrap)
  document.addEventListener("shown.bs.dropdown", function(e){
    var menu = e && e.target ? e.target.querySelector(".dropdown-menu") : null;
    if (!menu || !isOrgMenu(menu)) return;
    menu.classList.add("org-panel");
    document.body.classList.add("org-panel-open");
    addBackdrop();
  });

  document.addEventListener("hide.bs.dropdown", function(e){
    var menu = e && e.target ? e.target.querySelector(".dropdown-menu") : null;
    if (!menu || !isOrgMenu(menu)) return;
    document.body.classList.remove("org-panel-open");
    document.querySelectorAll(".org-panel-backdrop").forEach(function(n){ n.remove(); });
  });
})();
</script>
<!-- org-menu-global-hotfix:end -->
BLADE

inject_include() {
  local f="$1"
  [ -f "$f" ] || return 0
  # already there?
  grep -q "partials\.org_menu_hotfix" "$f" && { echo "Already included in $f"; return 0; }

  cp -a "$f" "$f.bak_${STAMP}"
  awk '
    BEGIN{done=0}
    /<\/body>/ && !done {
      print "    @includeIf('\''partials.org_menu_hotfix'\'')"
      print
      done=1
      next
    }
    {print}
  ' "$f.bak_${STAMP}" > "$f"
  echo "Injected include into $f"
}

# 2) Insert the include before </body> in likely layouts (non-destructive, with backups)
for f in \
  resources/views/org/argon/app.blade.php \
  resources/views/admin/argon/app.blade.php \
  resources/views/layouts/app.blade.php \
  resources/views/layouts/main.blade.php \
  resources/views/app.blade.php
do
  inject_include "$f"
done

# 3) Clear/cache views
php artisan view:clear >/dev/null || true
php artisan view:cache >/dev/null || true

echo "----- VERIFY -----"
grep -RIn "partials\.org_menu_hotfix" resources/views || true
echo "Partial at $PARTIAL"
echo "✅ Global hotfix v2 installed. Adjust --org-navbar-h / --org-panel-w in the partial if needed."
