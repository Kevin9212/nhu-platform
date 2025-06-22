<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatus extends Model {
    protected $table = 'user_statuses';
    public $timestamps = false;

    // 一個狀態屬於一個用戶
    public function user() {
        return $this->belongsTo(User::class);
    }
}
