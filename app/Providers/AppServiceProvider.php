<?php

namespace App\Providers;

use App\Models\IdleItem;
use App\Policies\IdleItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * The model to policy mappings for the application.
     * 應用程式的模型對應策略。
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

        // 新增：將 IdleItem 模型與 IdleItemPolicy 策略綁定在一起。
        // 這樣 Laravel 就知道在檢查 IdleItem 的權限時，要去參考 IdleItemPolicy 的規則。
        IdleItem::class => IdleItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * 註冊任何認證／授權服務。
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
