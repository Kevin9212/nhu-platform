<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model {
    const UPDATED_AT = null;

    public function conversation(): BelongsTo {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function idleItem(): BelongsTo {
        return $this->belongsTo(IdleItem::class);
    }

    public function attachments(): HasMany {
        return $this->hasMany(MessageAttachment::class);
    }
}