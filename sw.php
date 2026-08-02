<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$version = googa_app_version();
$cacheName = 'googa-static-' . preg_replace('/[^a-zA-Z0-9._-]/', '-', $version);
$files = [
    './assets/googa-mascot.png?v=' . rawurlencode($version),
    './assets/icon-192.png?v=' . rawurlencode($version),
    './assets/icon-512.png?v=' . rawurlencode($version),
    './styles.css?v=' . rawurlencode($version),
    './bank.js?v=' . rawurlencode($version),
    './app.js?v=' . rawurlencode($version),
    './assets/read-aloud.js?v=' . rawurlencode($version),
    './stories.css?v=' . rawurlencode($version),
    './story-bank.js?v=' . rawurlencode($version),
    './stories.js?v=' . rawurlencode($version),
    './culture-test.css?v=' . rawurlencode($version),
    './culture-test-bank.js?v=' . rawurlencode($version),
    './culture-test.js?v=' . rawurlencode($version),
    './assets/voices/narrator-ubax.png?v=' . rawurlencode($version),
    './assets/voices/narrator-muuse.png?v=' . rawurlencode($version),
    './assets/stories/diin-dawaco.png?v=' . rawurlencode($version),
    './assets/stories/wiil-waal.png?v=' . rawurlencode($version),
    './assets/stories/cigaal-shidaad.png?v=' . rawurlencode($version),
    './assets/stories/caraweelo.png?v=' . rawurlencode($version),
    './audio/ui/welcome.mp3',
];
?>
const CACHE = <?= json_encode($cacheName) ?>;
const FILES = <?= json_encode($files, JSON_UNESCAPED_SLASHES) ?>;

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE).then(cache => cache.addAll(FILES)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE).map(key => caches.delete(key)))).then(() => self.clients.claim())
  );
});

self.addEventListener('message', event => {
  if (event.data === 'SKIP_WAITING') self.skipWaiting();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  const isDynamic = url.pathname.endsWith('/version.php')
    || url.pathname.endsWith('/session.php')
    || url.pathname.endsWith('/index.php')
    || url.pathname.endsWith('/login.php')
    || url.pathname.endsWith('/owner.php')
    || url.pathname.endsWith('/owner-ambassador-report.php')
    || url.pathname.endsWith('/owner-ambassador-document.php')
    || url.pathname.endsWith('/owner-mode.php')
    || url.pathname.endsWith('/manifest.php')
    || url.pathname.endsWith('/api/auth.php')
    || url.pathname.endsWith('/auth.php')
    || url.pathname.endsWith('/access.php')
    || url.pathname.endsWith('/checkout.php')
    || url.pathname.endsWith('/success.php')
    || url.pathname.endsWith('/reset-password.php')
    || url.pathname.endsWith('/portal.php')
    || url.pathname.endsWith('/help.php')
    || url.pathname.endsWith('/ambassador.php')
    || url.pathname.endsWith('/ambassador-sign.php')
    || url.pathname.endsWith('/family.php')
    || url.pathname.endsWith('/family-api.php')
    || url.pathname.endsWith('/family-social.php')
    || url.pathname.endsWith('/family-social-api.php')
    || url.pathname.endsWith('/family-join.php')
    || url.pathname.endsWith('/family-pending.php')
    || url.pathname.endsWith('/family-pending-status.php')
    || url.pathname.endsWith('/family-session.php')
    || url.pathname.endsWith('/stories.php')
    || url.pathname.endsWith('/culture-test.php')
    || url.pathname.endsWith('/sw.php')
    || url.pathname.endsWith('/googa/')
    || url.pathname.endsWith('/googa');

  if (isDynamic) {
    event.respondWith(fetch(event.request));
    return;
  }

  event.respondWith(
    caches.match(event.request).then(cached => {
      if (cached) return cached;
      return fetch(event.request).then(response => {
        if (!response || response.status !== 200) return response;
        const copy = response.clone();
        caches.open(CACHE).then(cache => cache.put(event.request, copy));
        return response;
      });
    })
  );
});
