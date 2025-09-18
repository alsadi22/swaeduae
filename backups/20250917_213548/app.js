(function(){
  function 4191851(s,ctx){ return Array.from((ctx||document).querySelectorAll(s)); }
  function hideAll(except){
    .forEach(m=>{
      if (m===except) return;
      m.classList.add("hidden"); m.style.display="none";
    });
    4191851("[data-dd-toggle],[aria-controls],[data-bs-toggle=dropdown],.dropdown-toggle")
      .forEach(b=>b.setAttribute("aria-expanded","false"));
  }
  function { return (ctx||document).querySelectorAll(s); } // nodeList
  function q(s,ctx){ return (ctx||document).querySelector(s); }

  function targetMenu(btn){
    const id = btn.getAttribute("data-dd-toggle") || btn.getAttribute("aria-controls");
    if (id){ const el = document.getElementById(id); if (el) return el; }
    const sib = btn.nextElementSibling;
    if (sib && (sib.matches("[data-dd-menu],.dropdown-menu,[role=menu]"))) return sib;
    const box = btn.closest("[data-dd-container],.dropdown") || btn.parentElement;
    if (box){
      const el = q("[data-dd-menu], .dropdown-menu, [role=menu]", box);
      if (el) return el;
    }
    return null;
  }

  document.addEventListener("click", function(ev){
    const btn = ev.target.closest("[data-dd-toggle],[aria-controls],[data-bs-toggle=dropdown],.dropdown-toggle");
    if (btn){
      const menu = targetMenu(btn); if(!menu) return;
      const isHidden = menu.classList.contains("hidden") || getComputedStyle(menu).display==="none";
      hideAll(menu);
      if (isHidden){
        menu.classList.remove("hidden"); menu.style.display="";
        btn.setAttribute("aria-expanded","true");
        if (!menu.classList.contains("z-50")) menu.classList.add("z-50");
      } else {
        menu.classList.add("hidden"); menu.style.display="none";
        btn.setAttribute("aria-expanded","false");
      }
      ev.preventDefault();
      return;
    }
    if (!ev.target.closest("[data-dd-menu],.dropdown-menu,[role=menu]")) hideAll();
  });

  document.addEventListener("keydown", function(e){ if(e.key==="Escape") hideAll(); });

  document.addEventListener("DOMContentLoaded", function(){
    4191851("[data-dd-menu],.dropdown-menu,[role=menu]").forEach(m=>{
      if (getComputedStyle(m).display!=="none" && !m.classList.contains("hidden")){
        m.classList.add("hidden"); m.style.display="none";
      }
      if (!m.style.position) m.style.position = "absolute";
    });
  });
})();
"JS"

# Clear compiled views (not strictly needed for JS, but keeps layout cache-busting fresh)
php artisan view:clear >/dev/null 2>&1 || true
php artisan optimize:clear >/dev/null 2>&1 || true
sudo systemctl reload php8.3-fpm 2>/dev/null || true

# Confirm edge sees the new JS (new ETag / Last-Modified expected)
curl -sSI https://swaeduae.ae/assets/app.js | tr -d "\r" | egrep -i "HTTP/|etag|last-modified|cf-cache-status|cache-control" || true
