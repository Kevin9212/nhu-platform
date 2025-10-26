<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SimpleNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public ?string $text = null,
        public ?string $url = null,
    ) {}

    // 存到資料庫（對應你現有的 notifications 表）
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'text'  => $this->text,
            'url'   => $this->url,
        ];
    }

    // 可選：給 API 時使用（等同於上面的資料）
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
