<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * HTML：全部通知頁（含分頁）
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest('created_at')
            ->paginate(15); // 分頁比較穩

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 單筆設為已讀後前往對應連結
     * 注意：通知主鍵是 UUID（字串）
     */
    public function markAsRead(Request $request, string $id){
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->to(data_get($notification?->data, 'url', route('notifications.index')));
    }

    /**
     * 全部標示為已讀（小鈴鐺下拉或頁面用）
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->noContent(); // 204
    }

    /**
     * AJAX：小鈴鐺用的未讀數 + 最新幾筆（精簡欄位）
     */
    public function fetchUnread(Request $request)
    {
        $user = $request->user();

        $latest = $user->unreadNotifications()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function (DatabaseNotification $n) {
                $data = $n->data ?? [];
                return [
                    'id'    => $n->id, // UUID
                    'title' => (string)($data['title'] ?? ''),
                    'text'  => (string)($data['text']  ?? ''),
                    'url'   => (string)($data['url']   ?? route('home')),
                    'time'  => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $latest,
        ]);
    }
}
