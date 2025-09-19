<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ConversationController extends Controller {
    /**
     * 顯示收件匣（所有對話，預設選第一個）
     */
    public function index() {
        $conversations = Conversation::where('buyer_id', Auth::id())
            ->orWhere('seller_id', Auth::id())
            ->with(['buyer', 'seller'])
            ->get();

        return view('conversations.index', compact('conversations'));
    }


    /**
     * 顯示單一聊天室
     */
    public function show(Conversation $conversation) {
        $conversations = Conversation::where('buyer_id', Auth::id())
            ->orWhere('seller_id', Auth::id())
            ->with(['buyer', 'seller'])
            ->get();

        $otherUser = $conversation->buyer_id == Auth::id()
            ? $conversation->seller
            : $conversation->buyer;

        $conversation->load('messages.sender');

        return view('conversations.show', compact('conversation', 'conversations', 'otherUser'));
    }


    /**
     * 與指定使用者開始或打開現有的對話
     */
    public function start(User $user) {
        $authUser = Auth::user();

        if ($user->id === $authUser->id) {
            return redirect()->route('conversations.index')
                ->with('error', '不能與自己建立對話');
        }

        // 找現有對話
        $conversation = Conversation::where(function ($q) use ($authUser, $user) {
            $q->where('buyer_id', $authUser->id)->where('seller_id', $user->id);
        })->orWhere(function ($q) use ($authUser, $user) {
            $q->where('buyer_id', $user->id)->where('seller_id', $authUser->id);
        })->first();

        // 沒有的話就新建
        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id'  => $authUser->id,
                'seller_id' => $user->id,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }

    /**
     * 發送訊息
     */
    public function storeMessage(Request $request, Conversation $conversation) {
        // 驗證
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // 確保使用者是參與者
        if (!in_array(Auth::id(), [$conversation->buyer_id, $conversation->seller_id])) {
            abort(403, '您不能在這個對話中發送訊息');
        }

        // 建立訊息（這裡用 input() 避免你遇到的錯誤）
        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $request->input('content'),
        ]);

        // 更新對話時間，讓排序正確
        $conversation->touch();

        return back()->with('success', '訊息已送出！');
    }
}
