document.addEventListener('DOMContentLoaded',function(){
  // Tables -> Argon style if not styled
  document.querySelectorAll('table').forEach(t=>{
    if(!t.className.match(/\btable\b/)){
      t.classList.add('table','align-items-center','mb-0');
    }
  });
  // Buttons -> Bootstrap if plain <button> or .btn-less anchors
  document.querySelectorAll('button:not(.btn)').forEach(b=>b.classList.add('btn','btn-primary'));
  document.querySelectorAll('a.btnless, a.button').forEach(a=>a.classList.add('btn','btn-outline-primary'));
  // Inputs -> Bootstrap if missing
  document.querySelectorAll('input:not([type=checkbox]):not([type=radio]):not(.form-control), textarea:not(.form-control), select:not(.form-select)').forEach(el=>{
    if(el.tagName==='SELECT') el.classList.add('form-select'); else el.classList.add('form-control');
  });
  // Cards: wrap .page-section blocks (if used) into Argon cards
  document.querySelectorAll('.page-card').forEach(s=>{
    s.classList.add('card','shadow','border-0');
    if(!s.querySelector('.card-body')){ let body=document.createElement('div'); body.className='card-body'; while(s.firstChild) body.appendChild(s.firstChild); s.appendChild(body); }
  });
});
