<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model {
    protected $table = 'product_images';
    public $timestamps = false;

    // 一張圖片屬於一個商品
    public function item() {
        return $this->belongsTo(IdleItem::class, 'product_id', 'id');
    }
}
