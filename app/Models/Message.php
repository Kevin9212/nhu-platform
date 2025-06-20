<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    public $timestamps = false;

    // 訊息屬於一個聊天室
    public function conversation() {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }
    // 訊息的發送者
    public function sender() {
        return $this->belongsTo(User::class, 'sender_account', 'account');
    }
    // 訊息可能關聯一個商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_id', 'id');
    }
    // 訊息可能有多個附件
    public function attachments() {
        return $this->hasMany(MessageAttachment::class, 'message_id', 'id');
    }
}
