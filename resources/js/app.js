import './bootstrap';
import './chat';
import '../css/app.css';
import './member';
// console.log('app.js loaded'); // 臨時偵錯

// resources/js/app.js
window.NHU = window.NHU || {};
NHU.notify = (function () {
  const rootSel = '#nhu-notify';

  function els(root){
    return {
      panel:   root.querySelector('[data-nhu="panel"]'),
      badge:   root.querySelector('[data-nhu="badge"]'),
      loading: root.querySelector('[data-nhu="loading"]'),
      empty:   root.querySelector('[data-nhu="empty"]'),
      list:    root.querySelector('[data-nhu="list"]'),
    };
  }

  function toggle(e){
    e.preventDefault();
    const root = document.querySelector(rootSel);
    if (!root) return false;
    const {panel} = els(root);
    const open = panel.getAttribute('aria-hidden') === 'false';
    panel.setAttribute('aria-hidden', open ? 'true' : 'false');
    if (!open) load(); // 打開時載入
    return false;
  }

  async function load(){
    const root = document.querySelector(rootSel);
    if (!root) return;
    const {loading, empty, list, badge} = els(root);
    const url = root.getAttribute('data-fetch');
    if (!url) {
      loading.classList.add('is-hidden');
      empty.textContent = '通知功能暫時無法使用';
      empty.classList.remove('is-hidden');
      return;
    }
    loading.classList.remove('is-hidden');
    empty.classList.add('is-hidden');
    list.innerHTML = '';

    try {
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      (data.items || []).forEach(item => {
        const a = document.createElement('a');
        a.className = 'nhu-item' + (item.read ? '' : ' is-unread');
        a.href = item.url || '#';
        
        a.innerHTML = `
          <div class="nhu-item-title">${escapeHtml(item.title || '')}</div>
          <div class="nhu-item-text">${escapeHtml(item.text  || '')}</div>
          <div class="nhu-item-time">${escapeHtml(item.time  || '')}</div>
        `;
        list.appendChild(a);
      });

      if (!data.items || data.items.length === 0){
        empty.classList.remove('is-hidden');
      }

      const c = Number(data.count || 0);
      if (c > 0) {
        badge.classList.remove('is-hidden');
        badge.textContent = c;
      } else {
        badge.classList.add('is-hidden');
      }
    } catch (e){
      console.error('載入通知失敗', e);
      empty.textContent = `通知載入失敗（${e.message}）`;
      empty.classList.remove('is-hidden');
    } finally {
      loading.classList.add('is-hidden');
    }
  }

  async function readAll(e){
    e.preventDefault();
    const root = document.querySelector(rootSel);
    if (!root) return false;
    const {badge, list} = els(root);
    const url = root.getAttribute('data-readall');
    if(!url){
      return false;
    }
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept':'application/json'
        },
        credentials: 'same-origin',
      });
      if (res.ok){
        badge.classList.add('is-hidden');
        list.querySelectorAll('.nhu-item.is-unread').forEach(n => n.classList.remove('is-unread'));
      }
    } catch (e){ console.error(e); }
    return false;
  }

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  return { toggle, readAll };
})();
