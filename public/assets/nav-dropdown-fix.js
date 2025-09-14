(function(){
  function txt(n){return (n.textContent||"").replace(/\s+/g," ").trim();}
  function Q(s,r){return Array.from((r||document).querySelectorAll(s));}
  function findBtn(re){return Q("header a,header button,nav a,nav button,a,button").find(el=>re.test(txt(el)) && el.offsetParent);}
  function findMenu(b,re){if(!b) return null; let p=b.closest("header,nav,body")||document; return Q("div,ul,menu",p).find(n=>re.test(txt(n)));}
  function ensureLink(menu,label,url){
    let L=label.toLowerCase();
    let node=Q("a,button,li,div,span",menu).find(el=>{let x=txt(el).toLowerCase(); return x===L || x.indexOf(L)>-1;});
    if(!node) return;
    let a = node.tagName==="A" ? node : (node.closest && node.closest("a")) || (node.querySelector && node.querySelector("a"));
    if(a){
      let href=a.getAttribute("href")||"";
      if(!href || href==="#" || href.toLowerCase().startsWith("javascript:")) a.setAttribute("href", url);
      a.addEventListener("click", ev=>{ ev.stopPropagation(); }); // let default navigation happen
    } else {
      node.style.cursor="pointer";
      node.addEventListener("click", ev=>{ ev.stopPropagation(); window.location.assign(url); });
    }
  }
  function wire(btnText, probe){
    let b=findBtn(new RegExp("^\\s*"+btnText+"\\s*$","i")), m=findMenu(b,new RegExp(probe,"i")); if(!b||!m) return;
    function show(x){ m.style.display=x?"block":"none"; m.classList.toggle("hidden",!x); if(!m.style.zIndex) m.style.zIndex="1000"; }
    b.addEventListener("click", e=>{ e.preventDefault(); e.stopPropagation(); const disp=window.getComputedStyle(m).display; show(disp==="none"); });
    document.addEventListener("click", e=>{ if(!m.contains(e.target) && !b.contains(e.target)) show(false); });
    document.addEventListener("keydown", e=>{ if(e.key==="Escape") show(false); });
    ensureLink(m,"Volunteer Sign In","/login");
    ensureLink(m,"Organization Sign In","/org/login");
    ensureLink(m,"Volunteer Sign Up","/register");
    ensureLink(m,"Organization Sign Up","/org/register");
    ensureLink(m,"Profile Settings","/my/settings");
  }
  wire("Sign In","Volunteer Sign In");
  wire("Sign Up","Volunteer Sign Up");
})();