// public/js/member.js
document.addEventListener('DOMContentLoaded', () => {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    function switchTab(tabId) {
        // 關掉全部
        tabLinks.forEach(l => l.classList.remove('active'));
        tabPanes.forEach(p => p.classList.remove('active'));

        // link OK，pane 改用 id 找
        const activeLink = document.querySelector(`.tab-link[data-tab="${tabId}"]`);
        const activePane = document.getElementById(`tab-${tabId}`);

        if (activeLink && activePane) {
            activeLink.classList.add('active');
            activePane.classList.add('active');
            // 統一使用 same key
            localStorage.setItem('activeMemberTab', tabId);
        }
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            switchTab(link.dataset.tab);
        });
    });

    // 初始：看看有沒有記錄
    const saved = localStorage.getItem('activeMemberTab');
    const first = tabLinks[0].dataset.tab;
    switchTab(saved && document.getElementById(`tab-${saved}`) ? saved : first);
});
  