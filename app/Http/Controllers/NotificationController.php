<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * 顯示通知頁面
     */
    public function index()
    {
        $user = Auth::user();

        // 取得所有通知（已讀 + 未讀）
        $notifications = $user->notifications()->latest()->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 單一通知 → 點擊後標記已讀
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        // 如果通知有 url，就跳過去
        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    /**
     * 提供「小鈴鐺」用的未讀數量
     */
    public function fetchUnread()
    {
        $user = Auth::user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'notifications' => $user->unreadNotifications()->latest()->limit(5)->get(),
        ]);
    }
}
