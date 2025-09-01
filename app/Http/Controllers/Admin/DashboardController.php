<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** 
     * 顯示後台儀表板
     */
    public function index(){
        // 準備一些簡單的統計數據
        $stats = [
            'total_users' => User::count(),
            'total_items' => IdleItem::count(),
            'active_items' => IdleItem::where('idle_status', 1)->count(),
        ];
        return view('admin.dashboard', ['stats' => $stats]);
    }
}
