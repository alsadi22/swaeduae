(function(){
  const btn = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if(!btn || !nav) return;
  btn.addEventListener('click', ()=> {
    nav.setAttribute('data-open', nav.getAttribute('data-open')==='1' ? '0' : '1');
  });
})();
