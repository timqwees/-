/* eslint-env serviceworker */
/**
 * ФСС — service worker (PWA).
 *
 * Кэширует ТОЛЬКО статику (/public/assets/ и файлы css/js/img/шрифты).
 * HTML-документы не кэшируются: страницы динамические (сессии, заявки, персональные цены),
 * кэш документов приводил бы к показам устаревших данных и сломанной форме.
 * Версия кэша — в константе VERSION: при её смене старые кэши удаляются в activate.
 */
const VERSION = 'fss-20261024';
const ASSET_CACHE = 'fss-assets-' + VERSION;

self.addEventListener('install', (event) => {
	self.skipWaiting();
});

self.addEventListener('activate', (event) => {
	event.waitUntil(
		caches
			.keys()
			.then((keys) => Promise.all(keys.filter((k) => k !== ASSET_CACHE).map((k) => caches.delete(k))))
			.then(() => self.clients.claim())
	);
});

function isAsset(url) {
	return (
		url.pathname.indexOf('/public/assets/') === 0 ||
		/\.(css|js|mjs|png|jpe?g|webp|avif|svg|ico|woff2?|ttf)$/i.test(url.pathname)
	);
}

self.addEventListener('fetch', (event) => {
	const request = event.request;
	if (request.method !== 'GET') {
		return;
	}
	const url = new URL(request.url);
	if (url.origin !== self.location.origin) {
		return;
	}
	// Навигация (HTML) — всегда сеть: актуальные цены, заявки, сессии
	if (request.mode === 'navigate' || (request.headers.get('accept') || '').indexOf('text/html') === 0) {
		return;
	}
	if (!isAsset(url)) {
		return;
	}
	event.respondWith(
		caches.match(request).then((cached) => {
			const network = fetch(request)
				.then((response) => {
					if (response && response.ok && response.status === 200 && response.type === 'basic') {
						const copy = response.clone();
						caches.open(ASSET_CACHE).then((cache) => cache.put(request, copy));
					}
					return response;
				})
				.catch(() => cached);
			return cached || network;
		})
	);
});
