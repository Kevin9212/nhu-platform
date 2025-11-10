// resources/js/chat.js

// ------------- 小工具 -------------
const $ = (sel) => document.querySelector(sel);

const escapeHtml = (s) => (s || '').toString().replace(/[&<>"']/g, (m) => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[m]));

const autoGrowTextArea = (ta) => {
  if (!ta) return;
  ta.style.height = 'auto';
  ta.style.height = Math.min(ta.scrollHeight, 220) + 'px';
};

const makeScroller = (el, list) => {
  const settle = () => requestAnimationFrame(() => {
    if (!el) return;
    const last = list?.lastElementChild;
    if (last) {
      last.scrollIntoView({ block: 'end', inline: 'nearest' });
    }
    el.scrollTop = el.scrollHeight;
  });

  const nudge = () => {
    settle();
    setTimeout(settle, 180);
  };

  return { settle, nudge };
};

const attachWheelSync = (container, scroller) => {
  if (!container || !scroller) return;

  container.addEventListener('wheel', (event) => {
    if (event.defaultPrevented || event.ctrlKey) return;

    const deltaY = event.deltaY;
    if (!deltaY) return;

    const maxScrollTop = scroller.scrollHeight - scroller.clientHeight;
    if (maxScrollTop <= 0) return;

    const atTop = scroller.scrollTop <= 0;
    const atBottom = scroller.scrollTop >= maxScrollTop - 1;

    const scrollingUp = deltaY < 0;
    const scrollingDown = deltaY > 0;
    const shouldConsume = (scrollingDown && !atBottom) || (scrollingUp && !atTop);

    if (!shouldConsume) return;

    event.preventDefault();
    scroller.scrollTop += deltaY;
  }, { passive: false });
};

const nowISO = () => new Date().toISOString();

const buildOrderSummary = (raw) => {
  let data = {};
  try {
    data = typeof raw === 'string' ? JSON.parse(raw) : (raw || {});
  } catch (e) {
    console.warn('[chat] order summary parse failed', e);
  }

  const hasStatus = Boolean(data.status);
  const status = hasStatus ? data.status : '';
  const statusText = status === 'accepted'
    ? '✅ 賣家已接受議價'
    : status === 'rejected'
      ? '❌ 賣家已拒絕議價'
      : '⌛ 等待賣家回覆';
  const statusClass = status ? `chat-card__status--${status}` : '';

  let image = data.image || '';
  if (image && !/^https?:\/\//i.test(image)) {
    image = `/storage/${String(image).replace(/^\/+/, '')}`;
  }
  const imageHtml = image
    ? `<div class="chat-card__media"><img src="${escapeHtml(image)}" alt="商品圖片" loading="lazy"></div>`
    : '';

  const formatPrice = (v) => {
    if (v === undefined || v === null || v === '') return '';
    const num = Number(v);
    if (Number.isNaN(num)) return '';
    return Number(num).toLocaleString('zh-TW');
  };

  const original = formatPrice(data.item_price);
  const offer = formatPrice(data.offer_price);

  return `<div class="chat-bubble chat-bubble--card">
    <div class="chat-card chat-card--order">
      <div class="chat-card__header">
        <span class="chat-card__icon" aria-hidden="true">🧾</span>
        <span class="chat-card__title">訂單摘要</span>
      </div>
      <div class="chat-card__body">
        ${imageHtml}
        <div class="chat-card__details">
          <p class="chat-card__name">${escapeHtml(data.item_name || '')}</p>
          ${original ? `<p class="chat-card__price text-muted">原價：NT$ ${original}</p>` : ''}
          ${offer ? `<p class="chat-card__offer">議價：NT$ ${offer}</p>` : ''}
          ${status ? `<p class="chat-card__status ${statusClass}">${statusText}</p>` : ''}
        </div>
      </div>
    </div>
  </div>`;
};

// ------------------DOM--------------------------
document.addEventListener('DOMContentLoaded', () => {
  const form = $('#sendForm');
  const list = $('#messageList');
  const ta = $('#messageInput');
  const scrollerEl = $('#messageScroller');
  const threadEl = document.querySelector('.chat-thread');
  const sidebarEl = document.querySelector('.chat-sidebar');
  const sidebarList = document.querySelector('.chat-sidebar__list');
  const layoutEl = document.querySelector('.chat-layout');
  const openSidebarBtn = document.querySelector('[data-chat-open]');
  const closeSidebarBtns = document.querySelectorAll('[data-chat-close]');
  const overlayEl = document.querySelector('[data-chat-overlay]');

  const mobileMedia = window.matchMedia('(max-width: 1024px)');

  const setBodyLock = (locked) => {
    document.body.classList.toggle('chat-sidebar-open', Boolean(locked));
  };

  const reflectOverlay = (state) => {
    if (!overlayEl) return;
    overlayEl.classList.toggle('is-active', state === 'list');
  };

  const setMobileState = (state) => {
    if (!layoutEl) return;
    layoutEl.dataset.mobileState = state;

    if (layoutEl.dataset.mobile !== 'true') {
      return;
    }

    const isList = state === 'list';
    reflectOverlay(state);
    setBodyLock(isList);

    if (openSidebarBtn) {
      openSidebarBtn.setAttribute('aria-expanded', String(isList));
    }
  };

  const applyMobileLayout = () => {
    if (!layoutEl) return;

    if (!mobileMedia.matches) {
      delete layoutEl.dataset.mobile;
      reflectOverlay('thread');
      setBodyLock(false);
      if (openSidebarBtn) {
        openSidebarBtn.setAttribute('aria-expanded', 'false');
      }
      return;
    }

    layoutEl.dataset.mobile = 'true';
    const initialState = layoutEl.dataset.mobileState
      || layoutEl.dataset.mobileInitial
      || 'thread';
    setMobileState(initialState);
  };

  applyMobileLayout();
  if (typeof mobileMedia.addEventListener === 'function') {
    mobileMedia.addEventListener('change', applyMobileLayout);
  } else if (typeof mobileMedia.addListener === 'function') {
    mobileMedia.addListener(applyMobileLayout);
  }

  openSidebarBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    setMobileState('list');
  });

  closeSidebarBtns.forEach((btn) => {
    btn.addEventListener('click', (event) => {
      event.preventDefault();
      setMobileState('thread');
    });
  });

  overlayEl?.addEventListener('click', () => setMobileState('thread'));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setMobileState('thread');
    }
  });

  // 側邊搜尋（即便不在聊天頁也可以運作）
  const searchInput = document.querySelector('[data-chat-search]');
  const searchList = document.querySelector('[data-chat-list]');

  if (searchInput && searchList) {
    searchInput.addEventListener('input', () => {
      const keyword = searchInput.value.trim().toLowerCase();
      const items = searchList.querySelectorAll('[data-chat-item]');
      items.forEach((item) => {
        const haystack = item.dataset.searchText || '';
        const visible = !keyword || haystack.includes(keyword);
        item.classList.toggle('is-hidden', !visible);
      });
    });
  }

  if (!list || !form || !scrollerEl) return; // 不是聊天頁

  const myId = Number(form.dataset.myId || 0);
  const cid = Number(form.dataset.conversationId || 0);
  const CSRF = $('meta[name="csrf-token"]')?.content || '';
  const DEF_AVATAR = window.CHAT_DEFAULT_AVATAR || '/images/avatar-default.png';
  const { settle, nudge } = makeScroller(scrollerEl, list);

  attachWheelSync(threadEl, scrollerEl);
  attachWheelSync(sidebarEl, sidebarList);

  const keepAtBottom = () => {
    nudge();
  };

  keepAtBottom();
  window.addEventListener('load', keepAtBottom, { once: true });
  window.addEventListener('resize', settle);
  scrollerEl.addEventListener('load', keepAtBottom, true);

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(() => {
      const distanceFromBottom = scrollerEl.scrollHeight - scrollerEl.clientHeight - scrollerEl.scrollTop;
      if (distanceFromBottom < 40) {
        settle();
      }
    });
    observer.observe(list);
  }

  const appendMessage = (message) => {
    if (!message) return;
    const isMine = Number(message.sender_id) === myId;
    const nickname = isMine ? '我'
      : (message.sender?.nickname || message.sender?.account || '匿名');
    const avatarUrl = message.sender?.avatar_url || DEF_AVATAR;

    if (message.id) {
      const exists = list.querySelector(`[data-message-id="${message.id}"]`);
      if (exists) {
        return;
      }
    }

    const li = document.createElement('li');
    li.className = `chat-message ${isMine ? 'chat-message--mine' : 'chat-message--theirs'}`;
    li.dataset.messageId = message.id || '';

    const avatarHtml = `<img class="chat-message__avatar" src="${escapeHtml(avatarUrl)}" alt="${escapeHtml(nickname)}">`;

    const metaClass = `chat-message__meta ${isMine ? 'chat-message__meta--mine' : ''}`;
    const timeText = message.created_at_human || message.human_time || '剛剛';
    const rawTime = message.created_at_iso || message.created_at || message.created_at_full;
    let timeISO = '';
    if (rawTime && !Number.isNaN(Date.parse(rawTime))) {
      timeISO = new Date(rawTime).toISOString();
    }
    if (!timeISO) {
      timeISO = nowISO();
    }
    const timeTitle = message.created_at_full || message.created_at || timeISO;

    const textContent = escapeHtml(message.content || '').replace(/\n/g, '<br>');
    let bubbleHtml = `<div class="chat-bubble ${isMine ? 'chat-bubble--mine' : 'chat-bubble--theirs'}">
      <span class="chat-bubble__text">${textContent}</span>
    </div>`;

    if (message.msg_type === 'order_summary') {
      bubbleHtml = buildOrderSummary(message.content);
    }

    const statusHtml = isMine
      ? `<div class="chat-message__status ${message.read_at ? 'is-read' : ''}">${message.read_at ? '已讀' : '已送出'}</div>`
      : '';

    const bodyHtml = `
      <div class="${metaClass}">
        <span class="chat-message__name">${escapeHtml(nickname)}</span>
        <time class="chat-message__time" datetime="${escapeHtml(timeISO)}" title="${escapeHtml(timeTitle)}">${escapeHtml(timeText)}</time>
      </div>
      ${bubbleHtml}
      ${statusHtml}
    `;

    const bodyWrapper = `<div class="chat-message__body">${bodyHtml}</div>`;
    const markup = isMine
      ? `${bodyWrapper}${avatarHtml}`
      : `${avatarHtml}${bodyWrapper}`;

    li.innerHTML = markup;

    list.appendChild(li);
    nudge();
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const content = (ta.value || '').trim();
    if (!content) return;
    ta.value = content; // 修正 FormData 會取到未修剪前的值

    const fd = new FormData(form);

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json', // ★ 要 JSON
        },
        body: fd,
      });

      if (!res.ok) {
        // 看看後端到底回了什麼（通常是 HTML 或 302 後的 HTML）
        const text = await res.text();
        console.error('[chat] send not ok:', res.status, text.slice(0, 300));
        alert('送出失敗，請稍後重試');
        return;
      }

      // 確認真的是 JSON（防守一下）
      const ct = res.headers.get('content-type') || '';
      if (!ct.includes('application/json')) {
        const text = await res.text();
        console.error('[chat] not JSON:', text.slice(0, 300));
        alert('送出回應不是 JSON，請檢查後端');
        return;
      }

      const data = await res.json();

      if (data && data.message) {
        appendMessage(data.message);
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