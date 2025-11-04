<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;

class ConversationController extends Controller
{
    /**
     * 收件匣（我參與的所有對話）
     */
    public function index()
    {
        $uid = Auth::id();

        // 用群組 where，避免未來擴充條件時產生錯誤的 OR 組合
        $conversations = Conversation::query()
            ->where(function ($q) use ($uid) {
                $q->where('buyer_id', $uid)
                  ->orWhere('seller_id', $uid);
            })
            ->with(['buyer:id,nickname,account', 'seller:id,nickname,account'])
            ->latest('updated_at')
            ->get();

        return view('conversations.index', compact('conversations'));
    }

    /**
     * 顯示單一聊天室
     */
    public function show(Conversation $conversation)
    {
        $uid = Auth::id();

        // 只能由參與者查看
        if (!in_array($uid, [$conversation->buyer_id, $conversation->seller_id], true)) {
            abort(403, '您無權查看此對話');
        }

        // 左側清單
        $conversations = Conversation::query()
            ->where(function ($q) use ($uid) {
                $q->where('buyer_id', $uid)
                  ->orWhere('seller_id', $uid);
            })
            ->with(['buyer:id,nickname,account,avatar', 'seller:id,nickname,account,avatar'])
            ->latest('updated_at')
            ->get();

        // 對方使用者
        $otherUser = $conversation->buyer_id == $uid ? $conversation->seller : $conversation->buyer;

        // 載入訊息（時間正序）與發送者
        $conversation->load([
            'messages' => function ($q) {
                $q->orderBy('created_at', 'asc');
            },
            'messages.sender:id,nickname,account,avatar',
        ]);

        // 這兩個是你的 Blade 需要的
        $messages = $conversation->messages;
        $role     = ($uid === $conversation->buyer_id) ? 'buyer' : 'seller';

        Message::where('conversation_id',$conversation->id)
                ->where('sender_id','!=',auth()->id())
                ->whereNull('read_at')
                ->update(['read_at'=>now()]);

        return view('conversations.show', compact('conversation', 'conversations', 'otherUser', 'messages', 'role'));
    }


    /**
     * 發送訊息
     */
    public function storeMessage(Request $request, Conversation $conversation)
    {
        $uid = Auth::id();

        // 權限檢查
        if (!in_array($uid, [$conversation->buyer_id, $conversation->seller_id], true)) {
            abort(403, '您不能在這個對話中發送訊息');
        }

        // 驗證
        $data = $request->validate([
            'content'  => ['required', 'string', 'max:5000'],
            'msg_type' => ['nullable', 'string', 'max:50'],
        ]);

        // 建立訊息
        $message = $conversation->messages()->create([
            'sender_id'    => $uid,
            'idle_item_id' => $conversation->idle_item_id,
            'msg_type'     => $data['msg_type'] ?? 'text',
            'content'      => $data['content'],
            'is_recalled'  => false,
        ]);

        // 更新對話時間
        $conversation->touch();
        
        // 載入sender ,前端才有nickname / avatar_url
        $message->load('sender:id,nickname,account,avatar');

        // ✅ 廣播事件（通知其他用戶）
        broadcast(new MessageSent($message))->toOthers();

        $message->load('sender:id,nickname,account,avatar');
        // 若是 AJAX 請求可直接回傳 JSON
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest'
        || $request->expectsJson() || $request->wantsJson()) {
        return response()->json(['message' => $message]);
    }

        return back()->with('success', '訊息已送出！');
    }

    /**
     * 與指定使用者開始或打開現有的對話
     */
    public function start(User $user)
    {
        $me = Auth::user();

        if ($user->id === $me->id) {
            return redirect()->route('conversations.index')->with('error', '不能與自己建立對話');
        }

        // 查找既有對話
        $conversation = Conversation::where(function ($q) use ($me, $user) {
                $q->where('buyer_id', $me->id)->where('seller_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('buyer_id', $user->id)->where('seller_id', $me->id);
            })
            ->first();

        // 沒找到就建立（這裡沿用你原本的邏輯：我=buyer、對方=seller）
        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id'  => $me->id,
                'seller_id' => $user->id,
                // 如果你有「商品導向對話」，可考慮把 idle_item_id 接在這裡（從 query string 帶入）
                // 'idle_item_id' => (int) request('item_id') ?: null,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }
}
