<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model {
    public $timestamps = false;

    // 一個收藏紀錄屬於一個用戶
    public function user() {
        return $this->belongsTo(User::class, 'user_account', 'account');
    }
    // 一個收藏紀錄屬於一個商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_id', 'id');
    }
}