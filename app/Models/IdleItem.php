<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdleItem extends Model {
    use HasFactory;

    protected $fillable = [
        'idle_name',
        'idle_price',
        'idle_details',
        'category_id',
        'user_id',
        'idle_status',
    ];

    // 商品 → 使用者
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 商品 → 賣家（別名，避免錯誤）
    public function seller() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 商品 → 圖片
    public function images() {
        return $this->hasMany(ProductImage::class, 'idle_item_id');
    }

    // 商品 → 分類
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
public function orders()
{
    return $this->hasMany(Order::class, 'idle_item_id'); // ← 一定要用這個欄位
}


}
