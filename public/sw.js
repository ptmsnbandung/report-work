/* ═══════════════════════════════════════════════════════════════════
   PT MEDIA SOLUSI NETWORK (MSN) - WEB PUSH SERVICE WORKER
   ═══════════════════════════════════════════════════════════════════ */

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

// ── 1. RECEIVE PUSH EVENT FROM GOOGLE / FCM ──
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
        icon: payload.icon || '/assets/logo-msn BG Trans - Copy2.png',
        badge: payload.badge || '/assets/logo-msn BG Trans - Copy2.png',
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

// ── 2. HANDLE USER CLICK ON NOTIFICATION ──
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
