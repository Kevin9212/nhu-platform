<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    
    use HasFactory , Notifiable;
    /**
     * The attributes that are mass assignable.
     * 可以進行大量賦值的屬性（白名單）。
     * @var array<int,string>
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
     *  The attributes that shoule be hidden for serialization.
     *  應在序列化時被隱藏的屬性（黑名單）。
     * @var array<int,string>
     */
    protected $hidden = [
        'password','remember_roken',
    ];


    /**
     * Get the attributes that should be cast.
     * 取得應被轉換的屬性
     * 
     * @return array<string,string>
     */
    protected function casts():array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_time' => 'datetime',
        ];
    }

    /**
     * 一個用戶可以刊登多個商品
     */
    public function items():HasMany{
        return $this->hasMany(IdleItem::class);
    }

    /** 
     * 一個用戶可以有多筆訂單
    */
    public function orders():HasMany{
        return $this->hasMay(Order::class);
    }

    /**
     * 一個用戶可以有多筆訂單
     */
    public function favorites():HasMany{
        return $this->hasMany(Favorite::class);
    }

}
