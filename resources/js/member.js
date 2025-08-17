// resources/js/member.js
document.addEventListener('DOMContentLoaded', () => {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    /**
     * 根據傳入的 tabId 切換分頁
     * @param {string} tabId - 要顯示的分頁ID (例如 'profile', 'listings')
     */
    function switchTab(tabId) {
        // 隱藏所有分頁內容，並移除所有連結的 active 狀態
        tabLinks.forEach(link => link.classList.remove('active'));
        tabPanes.forEach(pane => pane.classList.remove('active'));

        // 找到對應的連結和內容區塊
        const activeLink = document.querySelector(`.tab-link[data-tab="${tabId}"]`);
        const activePane = document.getElementById(`tab-${tabId}`);

        // 如果找到了，就將它們設為 active (顯示出來)
        if (activeLink && activePane) {
            activeLink.classList.add('active');
            activePane.classList.add('active');
            // 將當前分頁ID存入 localStorage，以便下次載入時記住位置
            localStorage.setItem('activeMemberTab', tabId);
        }
    }

    // 為每一個導覽連結加上點擊事件
    tabLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault(); // 防止頁面跳轉
            const tabId = link.dataset.tab;
            switchTab(tabId);
        });
    });

    // 頁面載入時，檢查 localStorage 中是否有儲存的分頁紀錄
    const savedTab = localStorage.getItem('activeMemberTab');

    // 找到第一個可見的導覽連結作為預設值
    const firstTabLink = document.querySelector('.tab-link');
    const defaultTab = firstTabLink ? firstTabLink.dataset.tab : 'profile';

    // 如果有紀錄且該分頁存在，則顯示該分頁，否則顯示預設分頁
    const initialTab = (savedTab && document.getElementById(`tab-${savedTab}`))
        ? savedTab
        : defaultTab;

    switchTab(initialTab);
});