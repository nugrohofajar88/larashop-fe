// Service worker khusus panel admin (scope /admin/) - syarat teknis biar PWA bisa
// di-install ke homescreen. Sengaja MINIMAL (belum ada cache offline / push) - itu
// menyusul kalau fitur notifikasi order baru mulai dikerjakan.
const CACHE_NAME = 'admin-shell-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Network-first passthrough - admin panel selalu butuh data terbaru, jadi tidak
// disave ke cache. Handler ini cuma supaya browser mengenali SW ini valid utk PWA.
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});

// Notifikasi order baru dari BE (lihat OrderPaymentService::markPaid()).
self.addEventListener('push', (event) => {
    let data = { title: 'Sobat Akar Tani Kimia', body: 'Ada pembaruan baru.', data: {} };
    try {
        if (event.data) data = { ...data, ...event.data.json() };
    } catch (e) { /* payload bukan JSON valid - pakai default di atas */ }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/images/pwa-icon-192.png',
            badge: '/images/pwa-icon-192.png',
            data: data.data || {},
        })
    );
});

// Klik notifikasi -> fokus tab admin yang sudah terbuka, atau buka baru ke URL tujuan.
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = (event.notification.data && event.notification.data.url) || '/admin/dashboard';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(targetUrl) && 'focus' in client) return client.focus();
            }
            for (const client of clientList) {
                if ('focus' in client) { client.focus(); if ('navigate' in client) client.navigate(targetUrl); return; }
            }
            if (self.clients.openWindow) return self.clients.openWindow(targetUrl);
        })
    );
});
