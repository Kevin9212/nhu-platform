<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // 新增：引入 AuthorizesRequests Trait

class ConversationController extends Controller {
    use AuthorizesRequests; // 新增：在 Controller 中使用這個 Trait

    /**
     * 開始或顯示與指定使用者的對話。
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function startOrShow(User $user) {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 賣家不能跟自己聊天
        if ($currentUser->id === $user->id) {
            return redirect()->route('home')->with('error', '您不能與自己開始對話。');
        }

        // 尋找或建立買賣雙方之間的對話
        // firstOrCreate 會先嘗試尋找，如果找不到，就會用給定的資料建立一個新的
        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => $currentUser->id,
                'seller_id' => $user->id,
            ]
        );

        // 載入對話中的所有訊息以及訊息的發送者
        $conversation->load('messages.sender');

        return view('conversations.show', [
            'conversation' => $conversation,
            'receiver' => $user, // 將對方的資訊傳遞給視圖
        ]);
    }

    /**
     * 在指定的對話中儲存新訊息。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMessage(Request $request, Conversation $conversation) {
        // 權限檢查：確保目前使用者是這個對話的一方
        $this->authorize('view', $conversation);

        $request->validate(['content' => 'required|string|max:2000']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 建立新訊息
        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $request->content,
        ]);

        // 載入新訊息的發送者資訊，以便回傳給前端
        $message->load('sender');

        // 回傳 JSON 格式的訊息，方便前端 AJAX 處理
        return response()->json($message);
    }
}
