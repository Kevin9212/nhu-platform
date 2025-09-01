<?php

namespace App\Listeners;

use App\Events\NewMessageReceived;
use App\Notifications\NewMessageNotification; // 引入新的通知類別
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNewMessageNotification {
    /**
     * Handle the event.
     */
    public function handle(NewMessageReceived $event): void {
        $message = $event->message;
        $conversation = $message->conversation;

        // 找出訊息的接收者
        $receiver = $message->sender_id === $conversation->buyer_id
            ? $conversation->seller
            : $conversation->buyer;

        // 核心修正：使用 notify() 方法來發送通知
        $receiver->notify(new NewMessageNotification($message));
    }
}
