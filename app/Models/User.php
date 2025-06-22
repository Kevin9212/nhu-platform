<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable {
    /**
     * 因爲我們的主鍵不是預設的'id'
     * 所以必須加上這三行，告訴laravel 如何處理。
     *protected $primaryKey = 'account'; // 指定主鍵名稱
     *public $incrementing = false; // 主鍵不是自增的
     *protected $keyType = 'string'; // 主鍵類型是字符串  
     */
    // Laravel 預設的時間戳欄位是 created_at 和 updated_at
    // 如果你的資料表沒有這些欄位，可以關閉自動管理時間戳
    public $timestamps = false;


    // 告訴 Laravel 密碼的欄位叫'user_password'
    public function getAuthPassword() {
        return $this->user_password;
    }

    // 定義關聯：一對多User個商品
    public function idleItem() {
        // 參數：關聯的模型，外鍵欄位，本地主鍵欄位
        return $this->hasMany(IdleItem::class);
    }

    // 關聯：一個user有一個狀態
    public function status(): HasOne {
        return $this->hasOne(UserStatus::class);
    }
}
