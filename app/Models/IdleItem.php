<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdleItem extends Model {
    protected $table = 'idle_items';
    // 您的資料表中有 release_time，但沒有 updated_at
    // 如果您想讓 Laravel 自動管理 release_time，可以加上這行：
    const CREATED_AT = 'release_time';
    const UPDATED_AT = null; // 我們沒有 updated_at

    // 關聯：商品屬於一個賣家
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // 關聯：商品屬於一個分類
    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    // 關聯：商品有多張圖片
    public function images(): HasMany {
        return $this->hasMany(ProductImage::class);
    }
}