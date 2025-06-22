<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetupLocation extends Model {
    public $timestamps = false;

    public function idleItem(): BelongsTo {
        return $this->belongsTo(IdleItem::class);
    }
}
