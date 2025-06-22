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

        // 建立新使用者
        $user = new User();
        $user->account = $request->account;
        $user->user_password = Hash::make($request->password); // Hash密碼加密
        $user->nickname = $request->nickname;
        $user->user_phone = $request->user_phone;
        $user->save(); // 儲存使用者資料

        // 注冊成功后，把使用者導入登入頁面，并且附帶成功訊息
        return redirect()->route('login')->with('success', '註冊成功，請登入');
    }

    /**
     * 處理登入請求
     */
    public function login(Request $request) {
        $credentials = $request->validate([
            'account' => 'required|email',
            'password' => 'required',
        ]);

        /**
         * 1. 使用 Eloquent 模型查找使用者
         * 使用 User 模型查找帳號對應的使用者
         * 這裡假設帳號是唯一的，並且已經在資料庫中存在
         * 如果使用者不存在，則會返回 null
         */
        $user = User::where('account', $credentials['account'])->first();

        // 2. 檢查使用者是否存在，並手動用 Hash::check 驗證密碼
        if ($user && Hash::check($credentials['password'], $user->user_password)) {

            // 3. 如果驗證成功，手動將使用者登入
            Auth::login($user);

            // 重新生成 session ID
            $request->session()->regenerate();

            // 導向首頁，並附帶成功訊息
            return redirect()->intended('/')->with('success', '登入成功！');
        }
        // 如果驗證失敗，則返回上一頁，並附帶錯誤訊息
        return back()->withErrors([
            'account' => '帳號或密碼錯誤。',
        ])->onlyInput('account');
        /**嘗試使用Auth::attempt方法進行登入
         * 如果登入成功，Auth::attempt會返回true，否則返回false
         * 如果登入成功，會自動將使用者的ID存儲在session
         * 這樣就可以在後續的請求中使用Auth::check()
         * 來檢查使用者是否已經登入
         * 如果登入失敗，Auth::attempt會返回false
         *並且不會將使用者的ID存儲在session中
         */


        // 登入失敗，導向登入頁面並顯示錯誤信息
        return back()->withErrors([
            'account' => '賬號或密碼錯誤。',
        ])->onlyInput('account'); // only input('account') 只保留輸入的賬號
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
