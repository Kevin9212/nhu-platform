<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model {
    protected $table = 'system_logs';
    const UPDATED_AT = null;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}