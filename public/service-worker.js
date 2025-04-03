const cacheName = 'mi-pwa-cache-v1';
const staticAssets = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    // Agrega aquí otros recursos estáticos que quieras cachear
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(cacheName).then(function(cache) {
            console.log('ServiceWorker caching static assets');
            return cache.addAll(staticAssets);
        })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
});