<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'idle_item_id',
        'image_url',
    ];

    /**
     * 一張圖片屬於一個商品
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(IdleItem::class, 'idle_item_id');
    }
}
