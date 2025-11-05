// resources/js/member.js
// console.log('member.js loaded'); // 臨時偵錯

document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('.tab-link');
  const panes = {
    profile:   document.getElementById('tab-profile'),
    listings:  document.getElementById('tab-listings'),
    favorites: document.getElementById('tab-favorites'),
  };

  function show(tabId, pushHash = true) {
    // 顯示/隱藏：靠 hidden，避免 FOUC
    Object.values(panes).forEach(p => { if (p) p.hidden = true; });
    if (panes[tabId]) panes[tabId].hidden = false;

    // 左側 active 與 a11y
    links.forEach(a => {
      const on = a.dataset.tab === tabId;
      a.classList.toggle('active', on);
      a.setAttribute('aria-selected', on ? 'true' : 'false');
    });

    // 記錄 & 同步網址錨點
    localStorage.setItem('activeMemberTab', tabId);
    if (pushHash && location.hash !== '#' + tabId) {
      history.replaceState(null, '', '#' + tabId);
    }
  }

  // 點擊切換
  links.forEach(a => a.addEventListener('click', e => {
    e.preventDefault();
    show(a.dataset.tab);
  }));

  // 初始：hash > localStorage > 預設 profile
  const byHash = (location.hash || '').slice(1);
  const saved  = localStorage.getItem('activeMemberTab');
  const initial = panes[byHash] ? byHash : (panes[saved] ? saved : 'profile');
  show(initial, false);
});
