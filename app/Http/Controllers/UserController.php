<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Session;

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
        // 產生驗證碼並存入Session
        $captcha = $this ->generateCaptcha();
        Session::put('captcha', $captcha);

        return view('user.register',['captcha' => $captcha]);
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
                'unique:users,email',
                'regex:/@(nhu\.edu\.tw|ccu\.edu\.com\.tw)$/'
            ],
            'password' => ['required', 'confirmed', Password::defaults()], // 密碼欄位是必須的，最小長度為8個字符，並且需要確認密碼
            'nickname' => ['required', 'string', 'max:32'],  // 昵称是必需的，字符串类型，最大长度为32个字符
            'user_phone' => ['required','numeric', 'digits:10'], // 電話號碼欄位是必須的，並且只能包含數字，長度不超過10個字符
            // 新增加一個 Captcha 邏輯規則
            'captcha' => ['required','string',function ($attribute, $value, $fail) {
                if(strtoupper($value)!== Seeion::get('captcha')){
                    $newCaptcha = $this->generateCaptcha();
                    Session::put('captcha',$newCaptcha);
                    $fail('驗證碼錯誤，請重新輸入。');
                }
            }],

           /* 'g-recaptcha-response' => ['required',function($attribute,$value,$fail){
                // 驗證 Google reCAPTCHA
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value, // 前端傳回的 reCAPTCHA Token
                    'remoteip' => request()->ip(), // 可選，使用者的 IP 地址
                ]);
                if(!$response->json()['success']) {
                    $fail('驗證碼錯誤，請重新輸入。');
                }
            }],*/
        ], [
            // 自定錯誤訊息
            'account.required' => '學校信箱為必填項目。',
            'account.email' => '請輸入有效的信箱格式。',
            'account.unique' => '此信箱已被註冊，請嘗試登入或使用其他信箱。',
            'account.regex' => '信箱必須為南華大學 (@nhu.edu.tw) 或中正大學 (@ccu.edu.com.tw) 的信箱。',

            'password.required' => '密碼為必填項目。',
            'password.confirmed' => '兩次輸入的密碼不一致。',
            // Password::defaults() 的錯誤訊息的laravel自動產生的（例如：密碼必須8個字元、包含數字等）

            'nickname.required' => '暱稱為必填項目。',
            'nickname.max' => '暱稱長度不得超過32個字符。',

            'user_phone.required' => '電話號碼為必填項目。',
            'user_phone.numeric' => '電話號碼只能包含數字。',
            'user_phone.digits' => '電話號碼長度不得超過16個字符。',
            'captcha.required' => '請輸入驗證碼。',
        ]);

        // 驗證後清除Session中的Captcha
        Session::forget('captcha');
        // 使用 create 方法建立新使用者
        User::create([
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
    public function login(Request $request) {
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

    /**
     * AJAX：刷新驗證碼
     */
    public function refeshCaptcha(){
        $captcha = $this->generateCaptcha();
        Session::put('captcha', $captcha);
        return response()->json(['captcha' => $captcha]);
    }

    /**
     * 私有方法：產生一個 5 位數的驗證碼字串
     */
    private function generateCaptcha()
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        return substr(str_shuffle(str_repeat($chars, 5)), 0, 5);
    }
}
