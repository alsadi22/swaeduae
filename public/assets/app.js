// == PUBLIC-THEME-V1 ==
(function(){
  const store = {
    get(k){ try{ return JSON.parse(localStorage.getItem(k)); }catch(e){ return null; } },
    set(k,v){ try{ localStorage.setItem(k, JSON.stringify(v)); }catch(e){} }
  };

  // Contrast
  const btnContrast = document.querySelector("[data-contrast]");
  if(btnContrast){
    if(store.get("a11y.contrast")) document.body.classList.add("contrast-strong");
    btnContrast.addEventListener("click", ()=>{
      document.body.classList.toggle("contrast-strong");
      store.set("a11y.contrast", document.body.classList.contains("contrast-strong"));
    });
  }

  // Font size
  const s = document.documentElement;
  let size = store.get("a11y.font") || 16;
  s.style.fontSize = size + "px";
  const minus = document.querySelector("[data-fontminus]");
  const plus  = document.querySelector("[data-fontplus]");
  minus && minus.addEventListener("click", ()=>{ size=Math.max(14,size-1); s.style.fontSize=size+"px"; store.set("a11y.font", size); });
  plus  && plus.addEventListener("click", ()=>{ size=Math.min(20,size+1); s.style.fontSize=size+"px"; store.set("a11y.font", size); });

  // Mobile nav
  const toggle = document.querySelector(".nav-toggle");
  const nav = document.querySelector("[data-nav]");
  if(toggle && nav){
    toggle.addEventListener("click", ()=>{
      const open = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }
})();
