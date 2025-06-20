<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller {
    // 顯示註冊和登入頁面
    public function showAuthForm() {
        // 如果已登入，導向首頁
        if (Auth::check()) {
            return redirect('/');
        }
        return view('user.auth'); // 回傳 user/auth.blade.php 
    }

    // 處理註冊
    public function register(Request $request) {

        // 驗證登入資料 
        $request->validate([
            'account' => [
                'required',
                'email',
                'unique:users,account',
                'regex:/@(nhu\.edu\.tw|ccu\.edu\.com\.tw)$/'
            ],
            'password' => 'required|min:6',
            'nickname' => 'required|string|max:32',
            'user_phone' => 'required|string',
        ]);

        // 建立新使用者
        $user = new User();
        $user->account = $request->account;
        $user->user_password = Hash::make($request->password); // Hash密碼加密
        $user->nickname = $request->nickname;
        $user->user_phone = $request->user_phone;
        $user->save(); // 儲存使用者資料

        // 導向登入頁面並顯示成功信息
        return redirect()->route('user.form')->with('success', '註冊成功，請登入');
    }

    // 處理登入
    public function login(Request $request) {
        $credentials = $request->validate([
            'account' => 'required|email',
            'password' => 'required',
        ]);

        // 嘗試登入
        if (Auth::attempt($credentials)) {
            // 登入成功，導向首頁
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', '登入成功');
        }

        // 登入失敗，導向登入頁面並顯示錯誤信息
        return back()->withErrors([
            'account' => '賬號或密碼錯誤。',
        ])->onlyInput('account');
    }
    // 處理登出
    public function logout(Request $request) {
        Auth::logout(); // 登出使用者
        $request->session()->invalidate(); // 使會話無效
        $request->session()->regenerateToken(); // 重新生成CSRF令牌

        return redirect('/')->route('user.form')->with('success', '登出成功'); // 導向首頁並顯示成功信息
    }
}
