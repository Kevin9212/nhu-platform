<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller {
    /**
     * 顯示登入界面表單
     */
    // 顯示註冊和登入頁面
    public function showLoginForm() {
        // 如果使用者已經登入，就指向首頁
        if (Auth::check()) {
            return redirect()->route('home');
        }
        // 否則，顯示登入界面
        return view('user.login');
    }

    /**
     * 顯示注冊表單頁面
     */
    public function showRegistrationForm() {
        // 如果使用者已經登入，就指向首頁
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('user.register');
    }

    /**
     * 處理註冊請求
     */
    public function register(Request $request) {

        // 驗證登入資料 
        $request->validate([
            // 'account' 欄位是必須的，必須是有效的電子郵件格式必須以是以 @nhu.edu.tw 或 @ccu.edu.com.tw 結尾。
            'account' => [
                'required',
                'email',
                'unique:users,account',
                'regex:/@(nhu\.edu\.tw|ccu\.edu\.com\.tw)$/'
            ],
            'password' => 'required|min:6|confirmed', // 密碼欄位是必須的，最小長度為6個字符，並且需要確認密碼
            'nickname' => 'required|string|max:32',
            'user_phone' => 'required|string',
        ]);

        // 使用 create 方法建立新使用者
         $user = User::create([
            'account' => $request->account,
            'email' => $request->account, // 將 account 同時存入 email 欄位
            'password' => Hash::make($request->password), // 使用 'password' 欄位
            'nickname' => $request->nickname,
            'user_phone' => $request->user_phone,
        ]);
        

        // 注冊成功后，把使用者導入登入頁面，并且附帶成功訊息
        return redirect()->route('login')->with('success', '註冊成功，請登入');
    }

    /**
     * 處理登入請求
     */
    public function login(Request $request)
    {
        // 驗證輸入的資料
        $credentials = $request->validate([
            'account' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 使用 Auth::attempt 進行登入驗證，這是 Laravel 的標準做法
        // 注意：我們使用 'email' 欄位來進行驗證，值來自使用者輸入的 'account'
        if (Auth::attempt(['email' => $credentials['account'], 'password' => $credentials['password']], $request->filled('remember'))) {
            // 重新生成 session ID，防止 session fixation 攻擊
            $request->session()->regenerate();

            // 登入成功，導向使用者原本想去的頁面，如果沒有則導向首頁
            return redirect()->intended(route('home'))->with('success', '登入成功！');
        }

        // 如果驗證失敗，則返回登入頁面，並附帶錯誤訊息
        return back()->withErrors([
            'account' => '帳號或密碼錯誤。',
        ])->onlyInput('account');
    }


    /**
     * 處理登出請求
     */
    public function logout(Request $request) {
        Auth::logout(); // 登出使用者
        $request->session()->invalidate(); // 使會話無效
        $request->session()->regenerateToken(); // 重新生成CSRF token
        return redirect()->route('home')->with('success', '登出成功'); // 導向首頁並顯示成功信息
    }
}
