<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','sort_order'];

    /**
     * 一個分類底下可以有多個商品
     */
    public function items(): HasMany
    {
        return $this->hasMany(IdleItem::class);
    }
}
