<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    public $timestamps = false;

    // 一筆訂單屬於一個買家
    public function buyer() {
        return $this->belongsTo(User::class, 'user_account', 'account');
    }
    // 一筆訂單對應一個商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_id', 'id');
    }
    // 一筆訂單對應一筆評價
    public function rating() {
        return $this->hasOne(Rating::class, 'order_id', 'id');
    }
}
