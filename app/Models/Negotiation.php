<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model {
    use HasFactory;

    protected $fillable = [
        'idle_item_id',
        'buyer_id',
        'seller_id',
        'proposed_price',
        'status',
    ];

    /**
     * 與商品的關聯
     */
    public function item() {
        return $this->belongsTo(IdleItem::class, 'idle_item_id');
    }

    /**
     * 與買家的關聯
     */
    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 與賣家的關聯
     */
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
