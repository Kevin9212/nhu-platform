<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IdleItem;
use App\Models\Category;
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
        $userItems = $user->idleItems()->with('images')->latest('release_time')->get();

        // 取得使用者收藏的商品 (此功能尚未實現，暫時留空)
        $favoriteItems = collect();

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
            $avatarName = $user->id . '_' . time() . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->move(public_path('images/avatars'), $avatarName);
            $user->avatar = 'images/avatars/' . $avatarName;
        }

        $user->save();

        return back()->with('profile_success', '您的個人資料已成功更新！');
    }

}