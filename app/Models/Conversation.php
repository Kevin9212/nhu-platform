<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {
    // 此表有 created_at, updated_at，不需設定 $timestamps = false;

    // 聊天室的買家
    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_account', 'account');
    }
    // 聊天室的賣家
    public function seller() {
        return $this->belongsTo(User::class, 'seller_account', 'account');
    }
    // 聊天室有多則訊息
    public function messages() {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }
}
