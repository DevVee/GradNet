// ICCBI Alumni — Service Worker for Web Push Notifications
// Migrated from the legacy service-worker.js

const CACHE_NAME = 'iccbi-alumni-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// ── Push event: show notification ────────────────────────────────
self.addEventListener('push', (event) => {
    let data = {};
    if (event.data) {
        try { data = event.data.json(); } catch (e) { data = { title: 'ICCBI Alumni', body: event.data.text() }; }
    }

    const title   = data.title   || 'ICCBI Alumni';
    const options = {
        body:    data.body    || 'You have a new notification.',
        icon:    data.icon    || '/images/ICCLOGO.png',
        badge:   data.badge   || '/images/ICCLOGO.png',
        tag:     data.tag     || 'alumni-notification',
        data:    data.url     ? { url: data.url } : {},
        vibrate: [100, 50, 100],
        requireInteraction: false,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// ── Notification click: open the linked URL ───────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/notifications';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (const client of windowClients) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
