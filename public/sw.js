/* ═══════════════════════════════════════════════════════════════════
   PT MEDIA SOLUSI NETWORK (MSN) - PWA & WEB PUSH SERVICE WORKER
   ═══════════════════════════════════════════════════════════════════ */

const CACHE_NAME = 'ptmsn-report-v1.4';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/js/offline-sync.js',
    '/assets/icon-192.png',
    '/assets/icon-512.png',
    '/assets/work-report.png',
    '/assets/logo-msn BG Trans - Copy2.png',
    '/assets/logo-msn BG Trans.png',
    '/favicon.ico',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap'
];

self.addEventListener('install', function (event) {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(STATIC_ASSETS).catch(function () {
                // Ignore failure on dynamic CDN if offline during install
            });
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cache) {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

// ── 1. FETCH EVENT (NETWORK-FIRST WITH CACHE FALLBACK) ──
self.addEventListener('fetch', function (event) {
    if (event.request.method !== 'GET') return;

    // Skip non-http/https or chrome-extension requests
    if (!event.request.url.startsWith('http')) return;

    event.respondWith(
        fetch(event.request)
            .then(function (response) {
                // If valid response and is static asset, clone to cache
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseToCache = response.clone();
                    if (event.request.url.match(/\.(png|jpg|jpeg|svg|css|js|woff2|ico)$/i)) {
                        caches.open(CACHE_NAME).then(function (cache) {
                            cache.put(event.request, responseToCache);
                        });
                    }
                }
                return response;
            })
            .catch(function () {
                // Network failed, serve from cache if available
                return caches.match(event.request);
            })
    );
});

// ── 2. RECEIVE PUSH EVENT FROM GOOGLE / FCM ──
self.addEventListener('push', function (event) {
    if (!event.data) return;

    let payload = {};
    try {
        payload = event.data.json();
    } catch (e) {
        payload = {
            title: 'Notifikasi PT MSN',
            body: event.data.text(),
            url: '/'
        };
    }

    const title = payload.title || 'PT MSN Tiketing';
    const options = {
        body: payload.body || 'Pemberitahuan baru dari sistem tiketing gangguan.',
        vibrate: [150, 80, 150],
        renotify: true,
        tag: 'ptmsn-ticket-notif-' + (payload.data?.id_tiket || Date.now()),
        data: {
            url: payload.url || (payload.data?.url || '/'),
            time: Date.now()
        },
        actions: [
            { action: 'open', title: 'Buka Tiket' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ── 3. HANDLE USER CLICK ON NOTIFICATION (PWA APP ROUTING) ──
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const rawUrl = event.notification.data?.url || '/';
    const targetUrl = new URL(rawUrl, self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // If any PWA window / tab is open on this origin, navigate & focus it
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    if (client.url === targetUrl) {
                        return client.focus();
                    }
                    if ('navigate' in client) {
                        return client.navigate(targetUrl).then(function (c) {
                            return c ? c.focus() : client.focus();
                        });
                    }
                    return client.focus();
                }
            }
            // If no window is open, open the PWA app window
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
