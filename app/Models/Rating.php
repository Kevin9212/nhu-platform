<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    public $timestamps = false;

    // 評價者(買家)
    public function rater() {
        return $this->belongsTo(User::class, 'rater_id', 'account');
    }
    // 被評價者(賣家)
    public function rated() {
        return $this->belongsTo(User::class, 'rated_id', 'account');
    }
    // 評價屬於一筆訂單
    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}