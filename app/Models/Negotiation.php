<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Negotiation extends Model {
    public $incrementing = false;
    const CREATED_AT = 'negotiation_time';
    const UPDATED_AT = null;

    public function buyer(): BelongsTo {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function idleItem(): BelongsTo {
        return $this->belongsTo(IdleItem::class);
    }
}