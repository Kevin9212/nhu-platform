// resources/js/bootstrap.js
import axios from 'axios';
window.axios = axios;

// ---- CSRF / AJAX ----
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
if (CSRF) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;

/**
 * 只在頁面上存在 [data-enable-echo] 時才初始化 Echo（Reverb）
 * - 會員中心等不需要即時功能的頁面就不會載入 Echo
 * - 即便 Reverb 沒啟動，也不會把整個 app.js 中斷
 */
async function initEchoIfNeeded() {
  const marker = document.querySelector('[data-enable-echo]');
  if (!marker) return; // 這頁不需要 Echo

  try {
    const { default: Echo } = await import('laravel-echo');

    // 使用 Reverb；確保沒有 Pusher 相關需求
    // （不是 pusher，所以不需要 pusher-js，也不需要 window.Pusher）
    const key     = import.meta.env.VITE_REVERB_APP_KEY  || 'app-key';
    const host    = import.meta.env.VITE_REVERB_HOST     || window.location.hostname;
    const port    = Number(import.meta.env.VITE_REVERB_PORT ?? 8080);
    const scheme  = import.meta.env.VITE_REVERB_SCHEME   || 'http'; // 'http' or 'https'
    const tls     = scheme === 'https';

    window.Echo = new Echo({
      broadcaster: 'reverb',
      key: key,
      wsHost: host,
      wsPort: port,
      wssPort: port,
      forceTLS: tls,
      enabledTransports: ['ws', 'wss'],

      // 你的站點在 /nhu-platform/public 子目錄 → 明確指定授權端點
      authEndpoint: '/nhu-platform/public/broadcasting/auth',
      auth: { headers: { 'X-CSRF-TOKEN': CSRF } },
    });

    console.log('[Echo] enabled (Reverb) on this page');
    // 需要的話在這裡註冊監聽：
    // window.Echo.private('channel').listen('Event', (e) => console.log(e));
  } catch (err) {
    console.warn('[Echo] disabled:', err);
  }
}

document.addEventListener('DOMContentLoaded', initEchoIfNeeded);
