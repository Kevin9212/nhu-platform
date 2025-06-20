<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetupLocation extends Model {
    public $timestamps = false;

    // 一個地點屬於一個商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_id', 'id');
    }
}
