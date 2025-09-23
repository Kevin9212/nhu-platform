<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail {
    use HasFactory, Notifiable;

    /**
     * 可以進行大量賦值的屬性（白名單）。
     */
    protected $fillable = [
        'nickname',
        'account',
        'email',
        'password',
        'avatar',
        'user_phone',
        'last_login_time',
        'user_status',
        'role',
    ];

    /**
     * 應在序列化時被隱藏的屬性。
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 應被轉換的屬性。
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_time' => 'datetime',
            'banned_until' => 'datetime',
        ];
    }

    /**
     * 一個用戶可以刊登多個商品
     */
    public function items(): HasMany {
        // 核心修正：明確指定關聯的外鍵和本地鍵
        // 這告訴 Laravel: "idle_items 表的 user_id 欄位，對應到 users 表的 id 欄位"
        return $this->hasMany(IdleItem::class, 'user_id', 'id');
    }

    /**
     * 一個用戶可以有多筆訂單
     */
    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * 一個用戶可以收藏多個商品
     */
    public function favorites(): HasMany {
        return $this->hasMany(Favorite::class, 'user_id', 'id');
    }
}
