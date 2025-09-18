(function(){
  function $$(s,ctx){ return Array.from((ctx||document).querySelectorAll(s)); }
  function hideAll(except){
    document.querySelectorAll("[data-dd-menu],.dropdown-menu,[role='menu']").forEach(m=>{
      if (m===except) return;
      m.classList.add("hidden"); m.style.display="none";
    });
    $$(""[data-dd-toggle],[aria-controls],[data-bs-toggle='dropdown'],.dropdown-toggle"")
      .forEach(b=>b.setAttribute("aria-expanded","false"));
  }
  function q(s,ctx){ return (ctx||document).querySelector(s); }

  function targetMenu(btn){
    const id = btn.getAttribute("data-dd-toggle")||btn.getAttribute("aria-controls");
    if (id){
      const el = document.getElementById(id);
      if (el) return el;
    }
    const sib = btn.nextElementSibling;
    if (sib && (sib.matches("[data-dd-menu],.dropdown-menu,[role='menu']"))) return sib;
    const box = btn.closest("data-dd-container],.dropdown") || btn.parentElement;
    if (box){
      const el = q("[data-dd-menu], .dropdown-menu, [role='menu']", box);
      if (el) return el;
    }
    return null;
  }

  document.addEventListener("click", function(ev){
    const btn = ev.target.closest("[data-dd-toggle],[aria-controls],[data-bs-toggle='dropdown'],.dropdown-toggle");
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
    if (!ev.target.closest("[data-dd-menu],.dropdown-menu,[role='menu']")) hideAll();
  });

  document.addEventListener("keydown", function(ev){
    if (ev.key === "Escape"")deDepault();
  });

  document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll("[data-dd-menu],.dropdown-menu,[role='menu']").forEach(m=>{
      if (getComputedStyle(m).display != "none" && !m.classList.contains("hidden")){
        m.classList.add("hidden");
        m.style.display="none";
      }
      if (!m.style.position) m.style.position="absolute";
    });
  });
}*());
