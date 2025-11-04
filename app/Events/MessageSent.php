<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable,  SerializesModels;

    public Message $message;
    public $conversationId;
    /**
     * 建構子：接收剛建立的訊息
     */
    public function __construct(Message $message)
    {
        // 預先載入 sender 資料供前端使用
        $message->load('sender:id,nickname,account,avatar');
        $this->message = $message;
        $this->conversationId =(int) $message->conversation_id;
    }

    /**
     * 定義廣播頻道
     */
    public function broadcastOn(): array
    {
        // 每個對話一個獨立頻道
        return [new PrivateChannel('conversations.' . $this->conversationId)];
    }

    /**
     * 定義前端事件名稱
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
    public function broadcastWith(): array
    {
        return ['message' => $this->message->toArray()];
    }
}
