<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model {
    protected $table = 'system_logs';
    public $timestamps = false;

    // 日誌可能由某個用戶觸發
    public function user() {
        return $this->belongsTo(User::class);
    }
}
