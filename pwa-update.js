(() => {
  const config = window.GOOGA_PWA_UPDATE || {};
  const buttons = () => [...document.querySelectorAll('[data-pwa-update]')];
  let pendingVersion = '';
  const label = button => button.dataset.updateLabel || 'Oppdater';
  const show = () => buttons().forEach(button => { button.hidden = false; button.textContent = label(button); });
  async function check() {
    try {
      const response = await fetch(`${config.versionUrl}?ts=${Date.now()}`, { cache: 'no-store', credentials: 'same-origin' });
      const latest = await response.json();
      if (latest?.version && latest.version !== config.version) { pendingVersion = latest.version; show(); }
    } catch (_) {}
  }
  async function apply(button) {
    button.disabled = true;
    button.textContent = button.dataset.updatingLabel || 'Oppdaterer …';
    if ('serviceWorker' in navigator) {
      const registrations = await navigator.serviceWorker.getRegistrations();
      for (const registration of registrations) { if (registration.waiting) registration.waiting.postMessage('SKIP_WAITING'); await registration.update(); }
      const keys = await caches.keys();
      await Promise.all(keys.filter(key => key.startsWith('googa-')).map(key => caches.delete(key)));
    }
    const target = new URL(config.reloadUrl || location.href, location.href);
    target.searchParams.set('refresh', pendingVersion || config.version || 'latest');
    target.searchParams.set('t', Date.now());
    location.replace(target.toString());
  }
  document.addEventListener('click', event => { const button = event.target.closest('[data-pwa-update]'); if (button) apply(button); });
  check();
  setInterval(check, 60000);
})();
