<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model {

    use HasFactory;
    protected $fillable = [
      'buyer_id',
      'seller_id',  
    ];

    /**
     * 這個對話的買家
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 這個對話的賣家
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 一個對話中可以有多則訊息
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}