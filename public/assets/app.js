// PUBLIC-THEME-V1 — a11y + mobile nav
(function(){
  const store={get(k){try{return JSON.parse(localStorage.getItem(k))}catch(e){return null}},set(k,v){try{localStorage.setItem(k,JSON.stringify(v))}catch(e){}}};
  const btnContrast=document.querySelector("[data-contrast]");
  if(btnContrast){
    if(store.get("a11y.contrast")) document.body.classList.add("contrast-strong");
    btnContrast.addEventListener("click",()=>{ document.body.classList.toggle("contrast-strong"); store.set("a11y.contrast",document.body.classList.contains("contrast-strong")); });
  }
  const html=document.documentElement; let size=store.get("a11y.font")||16; html.style.fontSize=size+"px";
  const m=document.querySelector("[data-fontminus]"), p=document.querySelector("[data-fontplus]");
  m && m.addEventListener("click",()=>{ size=Math.max(14,size-1); html.style.fontSize=size+"px"; store.set("a11y.font",size); });
  p && p.addEventListener("click",()=>{ size=Math.min(20,size+1); html.style.fontSize=size+"px"; store.set("a11y.font",size); });
  const t=document.querySelector(".nav-toggle"), nav=document.querySelector("[data-nav]");
  if(t && nav){ t.addEventListener("click",()=>{ const open=nav.classList.toggle("open"); t.setAttribute("aria-expanded",open?"true":"false"); }); }
  try{ console.log("PUBLIC-THEME-V1"); }catch(e){}
})();
