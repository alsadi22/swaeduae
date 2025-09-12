// public/js/heartbeat.js
// Browser heartbeat for SwaedUAE (cookie-based Sanctum; no tokens in DOM)
(() => {
  const TAG = '[heartbeat]';
  const script = document.currentScript;
  const RADIUS_M = Number(script?.dataset?.geoRadius || 150);
  const VISIBLE_MS = 15000;  // 15s while tab visible
  const HIDDEN_MS  = 60000;  // 60s while hidden
  const MAX_ACCURACY = RADIUS_M * 2;

  let timer = null;
  let lastSend = 0;

  const log = (...a) => { /* console.debug(TAG, ...a); */ };

  const getCookie = (name) => {
    const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[-.$?*|{}()[\]\\/+^]/g, '\\$&') + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
  };

  const csrfReady = async () => {
    // Prime XSRF-TOKEN for cookie-based Sanctum
    try {
      await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
    } catch (_) {}
  };

  const canRun = async () => {
    if (!('geolocation' in navigator)) return false;
    try {
      if ('permissions' in navigator && navigator.permissions?.query) {
        const s = await navigator.permissions.query({ name: 'geolocation' });
        if (s.state === 'denied') return false;
      }
    } catch (_) {}
    return true;
  };

  const now = () => Date.now();

  const postHeartbeat = async (pos) => {
    const { latitude, longitude, accuracy } = pos.coords || {};
    if (typeof latitude !== 'number' || typeof longitude !== 'number') return;
    if (typeof accuracy === 'number' && accuracy > MAX_ACCURACY) {
      log('skip (low accuracy):', accuracy);
      return;
    }

    try {
      const xsrf = getCookie('XSRF-TOKEN');
      const res = await fetch('/api/v1/attendance/heartbeat', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(xsrf ? { 'X-XSRF-TOKEN': xsrf } : {}),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          lat: Number(latitude.toFixed(7)),
          lng: Number(longitude.toFixed(7)),
          accuracy: accuracy ?? null
        })
      });

      if (res.status === 204) {
        lastSend = now();
        return;
      }
      if (res.status === 401) {
        // Unauthenticated; stop trying silently
        stop();
        return;
      }
      if (res.status === 422) {
        // Bad payload; back off one cycle
        return;
      }
      // For other codes (429, 5xx), allow the loop to continue/backoff naturally
    } catch (e) {
      // Network error; ignore
    }
  };

  const tick = () => {
    const interval = document.hidden ? HIDDEN_MS : VISIBLE_MS;
    const opts = { enableHighAccuracy: true, timeout: 10000, maximumAge: 10000 };
    navigator.geolocation.getCurrentPosition(postHeartbeat, () => {}, opts);
    timer = setTimeout(tick, interval);
  };

  const start = async () => {
    if (!(await canRun())) return;
    await csrfReady();
    if (timer) clearTimeout(timer);
    tick();
    document.addEventListener('visibilitychange', () => {
      if (!timer) return;
      // Let the current timeout adjust naturally on next cycle
    });
  };

  const stop = () => {
    if (timer) clearTimeout(timer);
    timer = null;
  };

  // Only run on profile pages (guard with a body data-flag)
  const isProfile = document.body?.dataset?.page === 'my-profile';
  if (isProfile) start();
})();
