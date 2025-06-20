<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    public $timestamps = false;

    // 一個分類下有多個商品
    public function idleItems() {
        return $this->hasMany(IdleItem::class, 'idle_label', 'id');
    }
}
