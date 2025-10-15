<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserBannedNotification extends Notification
{
    use Queueable;

    protected $reason;

    /**
     * 建構子： 接受封禁原因or信息
     */
    public function __construct($reason = null)
    {
        $this->reason = $reason;
    }

    /**
     * 通知傳遞管道
     */
    public function via($notifiable): array
    {
        return ['database']; // 存資料庫即可，不用寄信
    }
    
    /**
     * 存到資料庫的格式
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => '帳號已被封鎖',
            'message' => '管理員封鎖了你的帳號' . ($this->reason ? "，原因：{$this->reason}" : ''),
        ];
    }
}
