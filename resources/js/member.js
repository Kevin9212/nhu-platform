// resources/js/member.js

// 會員中心分頁切換腳本
document.addEventListener('DOMContentLoaded', () => {
    // 沒有左側導覽就直接跳出，避免在別的頁面報錯
    const nav = document.querySelector('.member-nav');
    if (!nav) return;

    const links = nav.querySelectorAll('.tab-link');

    // 動態建立「tab 名稱 → 對應內容 pane」的映射
    const panes = {};
    links.forEach(link => {
        const tab = link.dataset.tab;      // 例如：profile / listings / favorites / orders / negotiations
        if (!tab) return;

        const pane = document.getElementById('tab-' + tab); // 對應 <section id="tab-orders"> 這種
        if (pane) {
            panes[tab] = pane;
            pane.classList.add('m-pane');   // 保險起見：確保有 m-pane class，CSS 會處理 hidden 顯示
        }
    });

    // 如果根本沒有 pane，就不做事
    if (Object.keys(panes).length === 0) return;
    const orderGuard = window.orderAccessGuard || {};

    function canEnterOrders(targetTab) {
        if (targetTab !== 'orders') return true;
        if (orderGuard.hasAcceptedNegotiation) return true;

        const fallbackTab = orderGuard.redirectTab || 'negotiations';
        const notice = orderGuard.message
            || '此議價尚未由賣家接受，請先回到議價總覽。';

        alert(notice);
        if (panes[fallbackTab]) {
            show(fallbackTab, true);
        }

        return false;
    }
    function show(tab, pushHash = true) {
        if (!panes[tab]) {
            // 要切換到一個不存在的 tab，直接忽略
            return;
        }
        if (!canEnterOrders(tab)) {
            return;
        }

        // 1) 右側內容顯示/隱藏
        Object.values(panes).forEach(p => {
            p.hidden = true;
        });
        panes[tab].hidden = false;

        // 2) 左側導覽 active 標記
        links.forEach(link => {
            const on = link.dataset.tab === tab;
            link.classList.toggle('active', on);
            link.setAttribute('aria-selected', on ? 'true' : 'false');
        });

        // 3) 記錄到 localStorage
        localStorage.setItem('activeMemberTab', tab);

        // 4) 更新網址 hash，但不要重載頁面
        if (pushHash) {
            const targetHash = '#' + tab;
            if (location.hash !== targetHash) {
                history.replaceState(null, '', targetHash);
            }
        }
    }

    // 點擊左側按鈕 → 切換 tab
    links.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const tab = link.dataset.tab;
            if (tab) show(tab, true);
        });
    });

    // 監聽 hash 變化（例如在會員中心裡點通知連結 /member#orders）
    window.addEventListener('hashchange', () => {
        const hashTab = (location.hash || '').slice(1);
        if (hashTab && panes[hashTab]) {
            show(hashTab, false);
        }
    });

    // ===== 初始 tab：hash > localStorage > 預設 profile =====
    const hashTab  = (location.hash || '').slice(1);          // 例如 "orders"
    const savedTab = localStorage.getItem('activeMemberTab'); // 上次停留的 tab
    let initial = 'profile';

    if (hashTab && panes[hashTab]) {
        initial = hashTab;
    } else if (savedTab && panes[savedTab]) {
        initial = savedTab;
    }

    show(initial, false);
});
