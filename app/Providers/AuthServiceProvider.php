<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\IdleItem;
use App\Policies\ConversationPolicy;
use App\Policies\IdleItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The model to policy mappings for the application.
     * 應用程式的模型對應策略。
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 將模型與對應的策略綁定在一起
        IdleItem::class => IdleItemPolicy::class,
        Conversation::class => ConversationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * 註冊任何認證／授權服務。
     */
    public function boot(): void {
        $this->registerPolicies();

        //
    }
}
