<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification {
    use Queueable;

    public $message;

    /**
    * Create a new notification instance.
    */
    public function __construct(Message $message) {
        $this->message = $message;
    }

    /**
    * Get the notification's delivery channels.
    */
    public function via(object $notifiable): array {
        // 我們只希望將通知存入資料庫
        return ['database'];
    }

    /**
    * Get the array representation of the notification.
    * 這是最終會被存成 JSON 格式，放到 data 欄位的內容
    */
    public function toArray(object $notifiable): array {
        return [
            'message' => \Illuminate\Support\Str::limit($this->message->content, 50),
            'sender_name' => $this->message->sender->nickname,
            'url' => route('conversation.start', $this->message->sender_id),
        ];
    }
}
