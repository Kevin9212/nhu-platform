<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * 取得使用者所有未讀的通知。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // 取得所有未讀通知，並限制最多 10 筆
        $notifications = $user->unreadNotifications()->latest()->limit(10)->get();

        return response()->json($notifications);
    }

    /**
     * 將使用者所有未讀的通知標示為已讀。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead() {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
