const STATIC_CACHE_NAME = 'wollradar-static-v3';
const PAGE_CACHE_NAME = 'wollradar-pages-v3';
const OFFLINE_URL = '/offline.html';
const PUBLIC_PAGE_PATHS = new Set([
    '/',
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/verify-email',
]);
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/favicon.svg',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/maskable-512.png',
    '/icons/apple-touch-icon.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );

    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key.startsWith('wollradar-') && ![STATIC_CACHE_NAME, PAGE_CACHE_NAME].includes(key))
                    .map((key) => caches.delete(key))
            )
        )
    );

    self.clients.claim();
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigationRequest(request));

        return;
    }

    const isStaticAsset =
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.startsWith('/build/');

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        staleWhileRevalidate(request, STATIC_CACHE_NAME)
    );
});

async function handleNavigationRequest(request) {
    const cache = await caches.open(PAGE_CACHE_NAME);
    const url = new URL(request.url);
    const shouldCachePage = PUBLIC_PAGE_PATHS.has(url.pathname);

    try {
        const response = await fetchWithTimeout(request, 8000);

        if (shouldCachePage && response.ok && response.headers.get('content-type')?.includes('text/html')) {
            cache.put(request, response.clone());
        }

        return response;
    } catch {
        const cached = shouldCachePage ? await cache.match(request) : null;

        if (cached) {
            return cached;
        }

        return (await caches.match(OFFLINE_URL)) || Response.error();
    }
}

async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);

    const networkFetch = fetch(request)
        .then((response) => {
            if (response.ok) {
                cache.put(request, response.clone());
            }

            return response;
        })
        .catch(() => cached);

    return cached || networkFetch;
}

function fetchWithTimeout(request, timeoutMs) {
    return Promise.race([
        fetch(request),
        new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Network timeout')), timeoutMs);
        }),
    ]);
}
