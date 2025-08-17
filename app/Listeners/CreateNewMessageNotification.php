<?php

namespace App\Listeners;

use App\Events\NewMessageReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNewMessageNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewMessageReceived $event): void {
        $message = $event->message;
        $conversation = $message->conversation;

        // 核心修正：先找出接收者，再建立通知
        $receiver = $message->sender_id === $conversation->buyer_id
            ? $conversation->seller
            : $conversation->buyer;

        // 建立通知
        $receiver->notifications()->create([
            'type' => 'new_message',
            'data' => json_encode([
                'message' => Str::limit($message->content, 50),
                'sender_name' => $message->sender->nickname,
                'url' => route('conversation.start', $message->sender_id),
            ]),
        ]);
    }
}
