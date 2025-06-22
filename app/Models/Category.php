<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model {
    public $timestamps = false;

    public function idleItems(): HasMany {
        // Laravel 會自動尋找 category_id 這個外鍵
        return $this->hasMany(IdleItem::class);
    }
}
