<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserUnbannedNotification extends Notification
{
    use Queueable;

    /**
     * 通知傳遞管道
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * 存到資料庫的格式
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => '帳號已恢復',
            'message' => '管理員已解除對你帳號的封禁，你現在可以正常使用平台。',
        ];
    }
}
