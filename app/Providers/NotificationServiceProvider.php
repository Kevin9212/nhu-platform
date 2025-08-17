<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        View::composer('partials.header',function($view){
            if(Auth::check()){
                $view->with('unreadNotifications',Auth::user()->unreadNotifications()->count());
            }else{
                $view->with('unreadNotifications',0);
            }
        })
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
    
}
