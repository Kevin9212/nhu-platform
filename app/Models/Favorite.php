<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model {
    
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    
    public function idleItem(): BelongsTo {
        return $this->belongsTo(IdleItem::class);
    }
}