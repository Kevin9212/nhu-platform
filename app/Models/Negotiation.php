<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model {
    // 注意：Laravel Eloquent 原生不支援複合主鍵
    // 這裡我們不設定主鍵，但關聯依舊可以正常運作
    public $incrementing = false;
    public $timestamps = false;

    // 議價的買家
    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_account', 'account');
    }
    // 議價的賣家
    public function seller() {
        return $this->belongsTo(User::class, 'seller_account', 'account');
    }
    // 議價的商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_id', 'id');
    }
}
