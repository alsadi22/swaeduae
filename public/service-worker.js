const CACHE='swaeduae-v3';
self.addEventListener('install',e=>{
  self.skipWaiting();
  e.waitUntil(caches.open(CACHE).then(c=>c.addAll(['/offline.html'])));
});
self.addEventListener('activate',e=>{
  e.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE).map(k=>caches.delete(k)))));
  self.clients.claim();
});
self.addEventListener('fetch',e=>{
  const req=e.request;
  if(req.method!=='GET') return;
  const accept=req.headers.get('accept')||'';
  if(accept.includes('text/html')){
    e.respondWith(fetch(req).catch(()=>caches.match('/offline.html')));
  }else{
    e.respondWith(caches.match(req).then(r=>r||fetch(req).then(res=>{
      const copy=res.clone();
      caches.open(CACHE).then(c=>c.put(req,copy));
      return res;
    })));
  }
});
