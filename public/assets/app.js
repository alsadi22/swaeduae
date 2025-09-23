(()=>{"use strict";
const $$=(s,r=document)=>Array.from(r.querySelectorAll(s));
const closeDD=()=>$$(".dropdown.dropdown-open").forEach(d=>d.classList.remove("dropdown-open"));
document.addEventListener("click",e=>{
  const t=e.target.closest("[data-dropdown-toggle]"); if(t){const d=t.closest(".dropdown"); if(d){d.classList.toggle("dropdown-open");} return;}
  const c=e.target.closest("[data-modal-close]"); if(c){const m=e.target.closest(".modal"); if(m){m.style.display="none";}
    const open=$$(".modal").some(x=>getComputedStyle(x).display!=="none");
    if(!open){document.documentElement.classList.remove("modal-open");
      const b=document.querySelector(".modal-backdrop"); if(b) b.style.display="none";}}
  if(!e.target.closest(".dropdown")) closeDD();
});
document.addEventListener("keydown",e=>{
  if(e.key==="Escape"){ closeDD(); $$(".modal").forEach(m=>m.style.display="none");
    document.documentElement.classList.remove("modal-open");
    const b=document.querySelector(".modal-backdrop"); if(b) b.style.display="none"; }
});
})();

// lazy fallback
document.addEventListener('DOMContentLoaded', function(){
  var imgs = document.querySelectorAll('img:not([loading])');
  imgs.forEach(function(img){
    try { img.setAttribute('loading','lazy'); img.setAttribute('decoding','async'); } catch(e){}
  });
});
