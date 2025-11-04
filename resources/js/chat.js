// resources/js/chat.js

// ------------- 小工具 -------------
function $(sel) { return document.querySelector(sel); }
function escapeHtml(s) {
  return (s || '').toString().replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[m]));
}
// 自動長高
function autoGrowTextArea(ta) {
  if (!ta) return;
  ta.style.height = 'auto';
  ta.style.height = Math.min(ta.scrollHeight, 220) + 'px';
}
// 捲到底
function makeScroller(el) {
  const scrollToBottom = () =>
    requestAnimationFrame(() => el && (el.scrollTop = el.scrollHeight));
  return { scrollToBottom };
}
// ------------------DOM--------------------------
// 讓這支檔案在任一聊天頁都能自我啟動：靠 data-* 取值
document.addEventListener('DOMContentLoaded', () => {
  const form = $('#sendForm');
  const list = $('#messageList');
  const ta = $('#messageInput');
  const scrollerEl = $('#messageScroller');
  
  if ( !list || !form || !scrollerEl) return; // 不是聊天頁就不做事

  // 從表單的 data-* 取必要資訊
  const myId = Number(form.dataset.myId || 0);
  const cid = Number(form.dataset.conversationId || 0);
  const CSRF = $('meta[name="csrf-token"]')?.content || '';
  const DEF_AVATAR = window.CHAT_DEFAULT_AVATAR || '/images/avatar-default.png';
  const {scrollToBottom} = makeScroller(scrollerEl);

  // 初次載入卷到底
  scrollToBottom();

  //-------- 渲染一筆訊息-----------
  function appendMessage(message) {
    // message 需要包含：sender_id, content, sender{ nickname/account/avatar_url }
    const isMine = Number(message.sender_id) === myId;

    const nickname = isMine ? '我'
      : (message.sender?.nickname || message.sender?.account || '匿名');

    const avatarUrl = (message.sender?.avatar_url) || DEF_AVATAR;

    const li = document.createElement('li');
    li.className = `mb-2 d-flex ${isMine ? 'justify-content-end' : 'justify-content-start'} align-items-end`;

    const avatar = `<img src="${avatarUrl}" class="rounded-circle ${isMine ? 'ms-2' : 'me-2'}"
                        style="width:32px;height:32px;object-fit:cover;">`;

    const meta = `<div class="text-muted" style="font-size:12px; ${isMine ? 'text-align:right;' : ''}">
                    ${escapeHtml(nickname)} · 剛剛
                  </div>`;

    const bubble = `<div class="${isMine ? 'text-white' : ''}"
                     style="padding:8px 12px; border-radius:16px;
                            ${isMine
                              ? 'background:#0d6efd; border-top-right-radius:4px;'
                              : 'background:#fff; border:1px solid #e5e7eb; border-top-left-radius:4px;'}">
                      <span class="d-block" style="white-space:pre-wrap; word-break:break-word;">
                        ${escapeHtml(message.content || '')}
                      </span>
                    </div>`;

    const body = `<div style="max-width:75%;">${meta}${bubble}</div>`;
    li.innerHTML = isMine ? (body + avatar) : (avatar + body);

    list.appendChild(li);
    scrollToBottom();
  }
// ------------- 送出（只清空、不本地 append） -------------
form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const content = (ta.value || '').trim();
  if (!content) return;

  const fd = new FormData(form);

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',          // ★ 要 JSON
      },
      body: fd
    });

    if (!res.ok) {
      // 看看後端到底回了什麼（通常是 HTML 或 302 後的 HTML）
      const text = await res.text();
      console.error('[chat] send not ok:', res.status, text.slice(0,300));
      alert('送出失敗，請稍後重試');
      return;
    }

    // 確認真的是 JSON（防守一下）
    const ct = res.headers.get('content-type') || '';
    if (!ct.includes('application/json')) {
      const text = await res.text();
      console.error('[chat] not JSON:', text.slice(0,300));
      alert('送出回應不是 JSON，請檢查後端');
      return;
    }

    const data = await res.json();

    if (data && data.message) {
      appendMessage(data.message);   // ★ 本地立即 append
    } else {
      console.warn('[chat] no message in payload', data);
    }

    ta.value = '';
    autoGrowTextArea(ta);
  } catch (err) {
    console.error('[chat] send failed:', err);
    alert('送出失敗，請檢查網路或稍後再試');
    // 這裡不要 form.submit()，避免整頁刷新
  }
});

  // 文字區塊：Shift+Enter 換行、Enter 送出
  ta?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      form.requestSubmit();
    }
  });

  ta?.addEventListener('input', () => autoGrowTextArea(ta));
  autoGrowTextArea(ta);

  // ------------- Echo 訂閱（只在這裡渲染，包含自己） -------------
  if (window.Echo && cid) {
    window.Echo.private(`conversations.${cid}`)
      .listen('.message.sent', (e) => {
        // 任何人（包含自己）的訊息都由這裡渲染一次
        // 後端事件需 load('sender')，資料結構：e.message.sender.avatar_url 等
        appendMessage(e.message);
      });
  } else {
    console.warn('[chat] Echo not available or conversationId missing');
  }
});