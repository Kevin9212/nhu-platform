<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model {
    const CREATED_AT = 'rating_time';
    const UPDATED_AT = null;

    public function rater(): BelongsTo {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function rated(): BelongsTo {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }
}