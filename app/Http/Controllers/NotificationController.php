<?php

namespace App\Http\Controllers;

use App\Notifications\NegotiationAcceptedNotification;
use App\Notifications\NewOfferNotification;
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

        return redirect()->to($this->resolveTargetUrl($notification));
    }

    /**
     * 全部標示為已讀（小鈴鐺下拉或頁面用）
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        if($request->expectsJson()){
            return response()->noContent(); // 204 for AJAX
            }
        return back()->with('status','已全部標記為已讀');    }

    /**
     * AJAX：小鈴鐺用的未讀數 + 最新幾筆（精簡欄位）
     */
    public function fetchUnread(Request $request)
    {
        $user = $request->user();

        $unreadCount = $user->unreadNotifications()->count();

        $latest = $user->notifications()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function (DatabaseNotification $n) {
                $data = $n->data ?? [];
                
                // 舊資料可能用 message，新資料用 text，這裡一起處理
                $text = $data['text'] ?? $data['message'] ?? '';

                return [
                    'id'    => $n->id, // UUID
                    'title' => (string)($data['title'] ?? ''),
                    'text'  => (string)$text,
                    'url'   => $this->resolveTargetUrl($n),
                    'time'  => optional($n->created_at)?->diffForHumans() ?? '',
                    'read'  => !is_null($n->read_at),
                ];
            });

        return response()->json([
            'count' => $unreadCount,
            'items' => $latest,
        ]);
    }
    
    /**
     * 根據通知類型，統一導向會員中心對應分頁
     */
    protected function resolveTargetUrl(?DatabaseNotification $notification): string
    {
        if (!$notification) {
            return route('notifications.index');
        }

        return match ($notification->type) {
            NewOfferNotification::class => route('member.index') . '#negotiations',
            NegotiationAcceptedNotification::class => route('member.index') . '#orders',
            default => (string) data_get($notification->data, 'url', route('notifications.index')),
        };
    }
}
