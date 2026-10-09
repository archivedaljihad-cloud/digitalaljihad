/* =====================================================
   SERVICE WORKER - AL-JIHAD DIGITAL SIGNAGE PWA
   Offline-First Resiliency & Intelligent Caching
   ===================================================== */
const CACHE_NAME = 'aljihad-signage-v5.8.16';
const STATIC_ASSETS = [
    './',
    'index.html',
    'prayer-mode.html',
    'petugas.html',
    'modeinput.html',
    'favicon.ico',
    'robots.txt',
    'sitemap.xml',
    'css/display-theme.css',
    'css/partials-theme.css',
    'css/sb-admin-2.min.css',
    'vendor/fontawesome-free/css/all.min.css',
    'vendor/jquery/jquery.min.js',
    'vendor/bootstrap/js/bootstrap.bundle.min.js',
    'js/supabase-config.js',
    'js/supabase-db.js',
    'js/admin-auth.js',
    'js/prayer-engine.js',
    'js/display-clock-ambient.js',
    'js/anti-idle.js',
    'slides/utama.html',
    'slides/keuangan.html',
    'slides/jumat.html',
    'slides/kas-jumat.html',
    'slides/pengumuman.html',
    'slides/keuangan-summary.html',
    'slides/qris.html',
    'slides/slide.html',
    'slides/ambulance.html',
    'slides/infaq.html',
    'slides/hikmah.html',
    'slides/qurban.html',
    'slides/yasin.html',
    'slides/live-mekah.html',
    'slides/live-madinah.html',
    'slides/live-mimbar.html',
    'slides/idul-fitri.html',
    'slides/idul-adha.html',
    'slides/ramadhan.html',
    'slides/kajian.html',
    'slides/agenda-rutin.html',
    'slides/undangan.html',
    'img/logo-aljihad-circle.png',
    'img/logo.png',
    'img/kop_pengumuman_jumat.png',
    'img/stempel_ttd_ketua_trans.png',
    'img/ttd_bendahara_trans.png',
    'img/ikonkitab.png',
    'img/bg-flyer-jumat.jpg',
    'img/IkonBicara.png',
    'img/IkonNgobrol.png',
    'img/IkonSilent.png',
    'img/IkonNelpon.png',
    'manifest.json'
];

// 1. Install Event: Cache Core Assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Pre-caching all static assets for offline resilience');
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Some assets failed to pre-cache (non-fatal):', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Cleanup All Old Caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => {
                    console.log('[SW] Menghapus cache lama:', key);
                    return caches.delete(key);
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Message Event: Force cache purge or skip waiting
self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
    if (event.data && event.data.action === 'clearCache') {
        caches.keys().then((keys) => {
            return Promise.all(keys.map((key) => caches.delete(key)));
        });
    }
});

// 3. Fetch Event: Network-First for HTML, Scripts & Slides; Stale-While-Revalidate for Heavy Media
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Abaikan request non-GET atau protokol non-http (chrome-extension dsb.)
    if (request.method !== 'GET' || !request.url.startsWith('http')) return;

    // Untuk API calls Supabase / REST API: biarkan fetch jaringan langsung
    if (request.url.includes('/rest/v1/') || request.url.includes('supabase.co')) {
        return;
    }

    // Strategi untuk Halaman HTML, Slide, & Script Logic (Network-First -> Cache Fallback)
    const isHtml = request.headers.get('accept') && request.headers.get('accept').includes('text/html');
    const isScript = request.url.includes('/js/') || request.url.endsWith('.js');
    const isAdminAsset = request.url.includes('admin') || request.url.includes('login') || request.url.includes('auth');

    if (isHtml || isScript || isAdminAsset) {
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
                    // Cegah recursive iframe nesting jika request adalah slide di dalam iframe
                    if (isHtml && !request.url.includes('/slides/') && !request.url.includes('prayer-mode')) {
                        return caches.match('index.html');
                    }
                    return new Response('<div style="color:#ffd700;background:#050505;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;text-align:center;"><div><h3>Memuat Konten Signage...</h3><p style="color:#94a3b8;font-size:14px;">Menghubungkan ke display server...</p></div></div>', {
                        headers: { 'Content-Type': 'text/html' }
                    });
                })
        );
        return;
    }

    // Strategi untuk Static Assets (CSS, Fonts, Images) -> Stale-While-Revalidate
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
