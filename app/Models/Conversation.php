<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model {
    // Laravel 會自動尋找 created_at 和 updated_at 這兩個欄位
    public function buyer(): BelongsTo {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    // Laravel 會自動尋找 seller_id 這個外鍵
    public function seller(): BelongsTo {
        return $this->belongsTo(User::class, 'seller_id');
    }
    // Laravel 會自動尋找 idle_item_id 這個外鍵
    public function messages(): HasMany {
        return $this->hasMany(Message::class);
    }
}