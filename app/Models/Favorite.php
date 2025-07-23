<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model {
    
    use HasFactory;
    protected $fillable = [
        'user_id',
        'idle_item_id',
    ];

    /**
     * 這個收藏紀錄屬於哪個用戶
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 這個收藏紀錄對應哪個商品
     */
    public function item(): BelongsTo {
        return $this->belongsTo(IdleItem::class,'idle_item_id');
    }
}