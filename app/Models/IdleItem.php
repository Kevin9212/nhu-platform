<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdleItem extends Model
{
    // 指定資料表名稱
    protected $table = 'idle_items';
    public $timestamps = false; // 如果資料表沒有 created_at 和 updated_at 欄位，則關閉自動管理時間戳

    // 一個商品屬於一個賣家
    public function seller() {
        return $this->belongsTo(User::class, 'user_account', 'account');
    }

    // 一個商品可能有一個成交買家
    public function buyer() {
        return $this->belongsTo(User::class, 'current_buyer_account', 'account');
    }

    // 一個商品屬於一個分類
    public function category() {
        return $this->belongsTo(Category::class, 'idle_label', 'id');
    }

    // 一個商品有多張圖片
    public function images() {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
    
    // 一個商品有多個面交地點
    public function meetupLocations() {
        return $this->hasMany(MeetupLocation::class, 'idle_id', 'id');
    }
}
