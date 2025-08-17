<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Conversation; // 新增：引入 Conversation 模型

class MemberController extends Controller {
    /**
     * 顯示會員中心頁面。
     */
    public function index() {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 取得使用者刊登的商品
        $userItems = $user->items()->with('images')->latest()->get();
        // 取得使用者收藏的商品
        $favoriteItems = $user->favorites()->with('item.images')->latest()->get();
        // 取得所有分類，供「新增商品」表單使用
        $categories = Category::all();

        // 核心功能：取得該使用者的所有對話
        // 無論是作為買家還是賣家，都撈取出來
        $conversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with([
                'buyer', // 預先載入買家資訊
                'seller', // 預先載入賣家資訊
                'messages' => function ($query) {
                    // 只載入最新的一則訊息，用來當作預覽
                    $query->latest()->limit(1);
                }
            ])
            ->latest('updated_at') // 讓有最新訊息的對話排在最上面
            ->get();

        return view('member.index', [
            'user' => $user,
            'favoriteItems' => $favoriteItems,
            'userItems' => $userItems,
            'categories' => $categories,
            'conversations' => $conversations, // 將對話資料傳遞給視圖
        ]);
    }

    /**
     * 更新使用者的個人資料。
     */
    public function updateProfile(Request $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nickname' => 'required|string|max:32',
            'user_phone' => 'required|string|max:16',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->nickname = $validated['nickname'];
        $user->user_phone = $validated['user_phone'];

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = $user->id . '_' . uniqid() . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->storeAs('avatars', $avatarName, 'public');
            $user->avatar = 'avatars/' . $avatarName;
        }

        $user->save();

        return back()->with('profile_success', '您的個人資料已成功更新！');
    }
}
