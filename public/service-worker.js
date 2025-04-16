const cacheName = 'mi-pwa-cache-v1';
const staticAssets = [
    '/',
    '/build/assets/app.css', // o el nombre completo si hay hash
    '/build/assets/app.js',
    '/manifest.json',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(cacheName).then(async (cache) => {
            try {
                await cache.addAll(staticAssets);
                console.log('Caching exitoso');
            } catch (err) {
                console.error('Error cacheando assets:', err);
            }
        })
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;

    // Ignorar peticiones externas como Google Maps API
    const isSameOrigin = new URL(request.url).origin === self.location.origin;

    if (isSameOrigin && request.method === 'GET') {
        // Cache First para recursos estáticos
        event.respondWith(
            caches.match(request).then(cached => {
                return cached || fetch(request).then(response => {
                    return caches.open(cacheName).then(cache => {
                        cache.put(request, response.clone());
                        return response;
                    });
                });
            }).catch(() => {
                // Podrías devolver una página offline.html aquí si quisieras
            })
        );
    } else if (isSameOrigin && request.method === 'POST') {
        // Aquí puedes almacenar la petición en IndexedDB (para sincronización futura)
        // O simplemente dejarla fallar silenciosamente si estás offline
        // Mejor aún: manejarla desde el frontend como ya empezamos a hacer con Dexie.js
    }
});
