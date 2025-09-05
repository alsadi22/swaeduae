self.addEventListener('install', e => {
    console.log('Service Worker Installed');
    e.waitUntil(
        caches.open('swaed-cache').then(cache => {
            return cache.addAll(['/']);
        })
    );
});

self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(response => response || fetch(e.request))
    );
});
