<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOfferNotification extends Notification
{
    use Queueable;

    protected $buyer;
    protected $item;

    public function __construct($buyer, $item){
        $this->buyer = $buyer;
        $this->item = $item;
    }

    public function via($notifiable): array{
        return ['database'];
    }

    public function toArray($notifiable): array{
        return [
            'title' => '有人對你的商品出價',
            'message' => "{$this->buyer->nickname} 對你的商品「{$this->item->idle_name}」提出了議價。",
            'item_id' => $this->item->id,
            'url' => route('conversations.index'), // 或 conversations.show
        ];
    }

}
