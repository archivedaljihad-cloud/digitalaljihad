/* =====================================================
   SERVICE WORKER - AL-JIHAD DIGITAL SIGNAGE PWA
   Offline-First Resiliency & Intelligent Caching
   ===================================================== */
const CACHE_NAME = 'aljihad-signage-v1.2';
const STATIC_ASSETS = [
    './',
    'index.html',
    'admin.html',
    'login.html',
    'favicon.ico',
    'css/display-theme.css',
    'css/partials-theme.css',
    'css/sb-admin-2.min.css',
    'vendor/fontawesome-free/css/all.min.css',
    'vendor/jquery/jquery.min.js',
    'vendor/bootstrap/js/bootstrap.bundle.min.js',
    'js/supabase-config.js',
    'js/admin-auth.js',
    'js/prayer-engine.js',
    'slides/utama.html',
    'slides/qurban.html',
    'slides/keuangan.html',
    'slides/jumat.html',
    'slides/ramadhan.html',
    'img/logo-aljihad-circle.png',
    'img/logo.png',
    'manifest.json'
];

// 1. Install Event: Cache Core Assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Pre-caching static assets for offline resilience');
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Some assets failed to pre-cache (non-fatal):', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Cleanup Old Caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Network-First with Cache Fallback for Pages & Cache-First for Assets
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Abaikan request non-GET atau protokol non-http (chrome-extension dsb.)
    if (request.method !== 'GET' || !request.url.startsWith('http')) return;

    // Untuk API calls Supabase / REST API: biarkan fetch jaringan langsung (fallback ditangani di JS app via localStorage)
    if (request.url.includes('/rest/v1/') || request.url.includes('supabase.co')) {
        return;
    }

    // Strategi untuk Halaman HTML & Slide (Network-First -> Cache Fallback)
    if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) return cachedResponse;
                    // Fallback jika halaman belum dicache: sajikan index.html
                    return caches.match('index.html');
                })
        );
        return;
    }

    // Strategi untuk Static Assets (CSS, JS, Fonts, Images) -> Stale-While-Revalidate
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            const fetchPromise = fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                }
                return networkResponse;
            }).catch(() => null);

            return cachedResponse || fetchPromise;
        })
    );
});
