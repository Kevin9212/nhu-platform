<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model {
    protected $table = 'product_images';
    public $timestamps = false;

    public function idleItem(): BelongsTo {
        // Laravel 會自動尋找 idle_item_id
        return $this->belongsTo(IdleItem::class);
    }
}