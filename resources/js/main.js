//resources/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notifications-dropdown');
    const notificationList = document.getElementById('notification-list');
    const notificationCount = document.getElementById('notification-count');

    if (bell) {
        bell.addEventListener('click', function (e) {
            e.preventDefault();

            // 切換下拉選單的顯示狀態
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';

            // 如果是第一次打開，或裡面只有「載入中」，就去抓取通知
            if (!isVisible && notificationList.querySelector('.loading-text')) {
                fetchNotifications();
            }
        });
    }

    // 點擊頁面其他地方時，關閉下拉選單
    document.addEventListener('click', function (e) {
        if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function fetchNotifications() {
        fetch('{{ route("notifications.index") }}')
            .then(response => response.json())
            .then(notifications => {
                notificationList.innerHTML = ''; // 清空「載入中」
                if (notifications.length > 0) {
                    notifications.forEach(notification => {
                        const data = JSON.parse(notification.data);
                        const item = document.createElement('a');
                        item.href = data.url;
                        item.className = 'notification-item';
                        item.innerHTML = `
                            <strong>${data.sender_name}</strong>
                            <p>${data.message}</p>
                            <span class="time">${new Date(notification.created_at).toLocaleTimeString('zh-TW', { hour: '2-digit', minute: '2-digit' })}</span>
                        `;
                        notificationList.appendChild(item);
                    });
                    // 標示為已讀
                    markNotificationsAsRead();
                } else {
                    notificationList.innerHTML = '<p class="no-notifications">目前沒有新通知</p>';
                }
            })
            .catch(error => {
                console.error('無法取得通知:', error);
                notificationList.innerHTML = '<p class="no-notifications">載入通知失敗</p>';
            });
    }

    function markNotificationsAsRead() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('{{ route("notifications.read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && notificationCount) {
                    // 成功後，隱藏紅點
                    notificationCount.style.display = 'none';
                }
            });
    }
});