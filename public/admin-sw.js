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
