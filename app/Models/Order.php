<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'idle_item_id',
        'order_price',
        'payment_status',
        'payment_way',
        'order_status',
        'cancel_reason',
        'meetup_location',
    ];

    protected function casts(): array
    {
        return [
            'meetup_location' => 'array',
        ];
    }

    // 這筆訂單屬於哪個用戶(買家)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 這筆訂單對應哪個商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(IdleItem::class, 'idle_item_id');
    }
}
