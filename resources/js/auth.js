// public/js/auth.js

/**
 * 顯示指定名稱的分頁內容
 * @param {string} tabName - 要顯示的分頁名稱 ('login' 或 'register')
 */
function showTab(tabName) {
    // 移除所有標籤和表單的 'active' class
    document.querySelectorAll('.auth-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));

    // 為被點擊的標籤和對應的表單加上 'active' class
    const activeTab = document.getElementById('tab-' + tabName);
    const activeForm = document.getElementById('form-' + tabName);

    if (activeTab) {
        activeTab.classList.add('active');
    }
    if (activeForm) {
        activeForm.classList.add('active');
    }
}

/**
 * 當頁面 DOM 載入完成後執行的事件監聽器
 */
document.addEventListener('DOMContentLoaded', () => {
    // 預設顯示登入分頁
    showTab('login');
});