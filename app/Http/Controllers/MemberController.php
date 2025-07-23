<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\IdleItem;
use App\Models\User;

class MemberController extends Controller
{
    /**
     * 顯示會員中心頁面。
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 取得使用者刊登的商品
        $userItems = $user->items()->with('images')->latest()->get();

        // 取得使用者收藏的商品 (此功能尚未實現，暫時留空)
        $favoriteItems = $user->favorites()->with('item.images')->latest()->get();

        // 取得所有分類，供「新增商品」表單使用
        $categories = Category::all();

        return view('member.index', [
            'user' => $user,
            'favoriteItems' => $favoriteItems,
            'userItems' => $userItems,
            'categories' => $categories,
        ]);
    }

    /**
     * 更新使用者的個人資料。
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nickname' => 'required|string|max:32',
            'user_phone' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->nickname = $validated['nickname'];
        $user->user_phone = $validated['user_phone'];

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            // 建立一個更安全、不易重複的檔案名稱
            $avatarName = $user->id . '_' . uniqid() . '.' . $avatarFile->getClientOriginalExtension();
            // 將檔案移動到 public/storage/avatars
            $avatarFile->storeAs('avatars', $avatarName, 'public');
            // 儲存相對路徑
            $user->avatar = 'storage/avatars/' . $avatarName;
        }

        $user->save();

        return back()->with('profile_success', '您的個人資料已成功更新！');
    }

}