<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'idle_item_id',
        'msg_type',
        'content',
        'is_recalled',
    ];

    /**
     * 這則訊息屬於哪個對話
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * 這則訊息的發送者
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * 這則訊息關聯哪個商品 (可以為空)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(IdleItem::class, 'idle_item_id');
    }
}
