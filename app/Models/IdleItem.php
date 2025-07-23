<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdleItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 可以進行大量賦值的屬性（白名單）。
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // 確保 user_id 在白名單中
        'category_id',
        'current_buyer_id',
        'idle_name',
        'idle_details',
        'idle_price',
        'idle_status',
        'is_rental',
        'room_type',
        'pets_allowed',
        'cooking_allowed',
        'rental_rules',
        'equipment',
        'meetup_location',
    ];

    /**
     * The attributes that should be cast.
     * 應被轉換的屬性。
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meetup_location' => 'array',
            'pets_allowed' => 'boolean',
            'cooking_allowed' => 'boolean',
            'is_rental' => 'boolean',
        ];
    }

    /**
     * 取得該商品的賣家
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 取得該商品的分類
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 取得該商品的圖片 (一個商品可以有多張圖片)
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * 取得該商品的當前買家 (如果有的話)
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_buyer_id');
    }
}
