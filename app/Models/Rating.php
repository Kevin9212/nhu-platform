<?php

namespace App\Models;

use Illuminate\Datebase\Eloquent\Factories\HasFactory;a
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model {
    use HasFactory;

    protected $fillable = [
        'order_id',
        'rater_id',
        'rated_id',
        'score',
        'comment',
    ]
    
    /**
     * 做出評價的人 (買家)
     */
    public function rater(): BelongsTo {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * 被評價的人 (賣家)
     */
    public function rated(): BelongsTo {
        return $this->belongsTo(User::class, 'rated_id');
    }

    /**
     * 這筆評價對應哪張訂單
     */
    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }
}