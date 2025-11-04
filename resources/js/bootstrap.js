// resources/js/bootstrap.js
import axios from 'axios';
window.axios = axios;

// AJAX / CSRF
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
if (CSRF) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;
}

// ---- Echo + Reverb (不要載 pusher-js) ----
import Echo from 'laravel-echo';

// 不是用 pusher，所以保險關掉它
window.Pusher = undefined;

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY || 'app-key',
  wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
  wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 8080,
  wssPort: Number(import.meta.env.VITE_REVERB_PORT) || 8080,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],

  // ★ 你站在子目錄：一定要指定 authEndpoint，否則私有頻道授權會 404/403
  authEndpoint: '/nhu-platform/public/broadcasting/auth',
  auth: {
    headers: { 'X-CSRF-TOKEN': CSRF },
  },
});
