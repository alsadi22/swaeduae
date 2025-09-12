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
