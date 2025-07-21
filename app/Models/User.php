<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable {
    // 因為您的 users 資料表沒有 created_at 和 updated_at，所以保留這行
    public $timestamps = false;

    /**
     * 告訴 Laravel 我們的密碼欄位叫做 'user_password'。
     * @return string
     */
    public function getAuthPassword(): string {
        return $this->user_password;
    }

    /**
     * 定義關聯：一個 User 可以有多個刊登商品 (IdleItem)。
     * 我們使用複數的 idleItems() 來表示「一對多」的關係。
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function idleItems(): HasMany {
        // Laravel 會自動根據方法名稱，去尋找 user_id 這個外鍵
        return $this->hasMany(IdleItem::class);
    }

    /**
     * 定義關聯：一個 User 有一個狀態 (UserStatus)。
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status(): HasOne {
        // Laravel 會自動尋找 user_id 這個外鍵
        return $this->hasOne(UserStatus::class);
    }
    
}
