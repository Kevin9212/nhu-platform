<?php

namespace App\Notifications;

use App\Models\IdleItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NegotiationAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected IdleItem $item,
        protected $price
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => '賣家已接受議價',
            'message' => "您的商品「{$this->item->idle_name}」議價已被接受，成交價為 NT$ {$this->price}。",
            'item_id' => $this->item->id,
            'url' => route('member.index') . '#orders',
        ];
    }
}