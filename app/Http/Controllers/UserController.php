<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
            'user_phone' => ['required','numeric', 'digits:10'], // 電話號碼欄位是必須的，並且只能包含10位數字
            // 新增加一個 Captcha 邏輯規則
            'captcha' => ['required','string',function ($attribute, $value, $fail) {
                if(strtoupper($value)!== Session::get('captcha')){
                    $newCaptcha = $this->generateCaptcha();
                    Session::put('captcha',$newCaptcha);
                    $fail('驗證碼錯誤，請重新輸入。');
                }
            }],


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
            'user_phone.digits' => '電話號碼長度必須為 10 位數字。',
            'captcha.required' => '請輸入驗證碼。',
        ]);

        // 驗證後清除Session中的Captcha
        Session::forget('captcha');
        // 使用 create 方法建立新使用者
        $user = User::create([
            'account' => $request->account,
            'email' => $request->account, // 將 account 同時存入 email 欄位
            'password' => Hash::make($request->password), // 使用 'password' 欄位
            'nickname' => $request->nickname,
            'user_phone' => $request->user_phone,
        ]);

        Session::put('pending_verification_email', $user->email);

        $error = null;
        if (!$this->sendVerificationCode($user, true, $error)) {
            return redirect()->route('register.verify.form', ['email' => $user->email])
                ->withErrors(['code' => $error ?? '驗證碼寄送失敗，請稍後再試。']);
        }

        // 注冊成功后，把使用者導入驗證頁面
        return redirect()->route('register.verify.form', ['email' => $user->email])
            ->with('success', '註冊成功！我們已寄出驗證碼到您的學校信箱，請於 10 分鐘內完成驗證。');
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

        if (Auth::attempt(['email' => $credentials['account'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                $error = null;
                $sent = $this->sendVerificationCode($user, true, $error);

                Auth::logout();
                Session::put('pending_verification_email', $user->email);

                if (!$sent) {
                    return back()->withErrors([
                        'account' => $error ?? '尚未完成信箱驗證，且驗證碼寄送失敗，請稍後再試。',
                    ])->onlyInput('account');
                }

                return redirect()->route('register.verify.form', ['email' => $user->email])
                    ->withErrors(['account' => '尚未完成信箱驗證，已重新寄送驗證碼。請輸入驗證碼後再登入。']);
            }

            // 檢查是否被封禁
            if ($user->user_status === 'banned') {
                Auth::logout(); // 立刻登出
                return back()->withErrors([
                    'account' => '此帳號已被封禁，請聯絡管理員。',
                ])->onlyInput('account');
            }

            return redirect()->intended(route('home'))->with('success', '登入成功！');
        }

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
     * 顯示註冊後的驗證碼填寫頁面
     */
    public function showVerificationCodeForm(Request $request)
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $email = $request->query('email') ?? Session::get('pending_verification_email');

        if (!$email) {
            return redirect()->route('register')->withErrors(['account' => '請先完成註冊資料填寫。']);
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    /**
     * 驗證註冊驗證碼
     */
    public function verifyEmailCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => '找不到此信箱的註冊資料。']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', '信箱已驗證，請直接登入。');
        }

        $record = DB::table('email_verification_codes')->where('email', $data['email'])->first();

        if (!$record) {
            return back()->withErrors(['code' => '尚未產生驗證碼，請重新寄送。']);
        }

        if (now()->greaterThan(Carbon::parse($record->expires_at))) {
            return back()->withErrors(['code' => '驗證碼已過期，請重新寄送。']);
        }

        if (!Hash::check($data['code'], $record->code)) {
            return back()->withErrors(['code' => '驗證碼錯誤，請再試一次。']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        DB::table('email_verification_codes')->where('email', $data['email'])->delete();
        Session::forget('pending_verification_email');

        return redirect()->route('login')->with('success', '驗證成功，請登入。');
    }

    /**
     * 重新寄送註冊驗證碼
     */
    public function resendVerificationCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => '找不到此信箱的註冊資料。']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', '信箱已驗證，請直接登入。');
        }

        $error = null;

        if (!$this->sendVerificationCode($user, false, $error)) {
            return back()->withErrors(['code' => $error ?? '系統剛寄出驗證碼，請稍候再試。']);
        }

        Session::put('pending_verification_email', $user->email);

        return back()->with('status', '已重新寄出驗證碼，請查收信箱。');
    }

    /**
     * AJAX：刷新驗證碼
     */
    public function refreshCaptcha(){
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

    /**
     * 產生並寄送註冊驗證碼
     */
    private function sendVerificationCode(User $user, bool $force = false, ?string &$error = null): bool
    {
        $existing = DB::table('email_verification_codes')
            ->where('email', $user->email)
            ->first();

        if (!$force && $existing && Carbon::parse($existing->updated_at)->gt(now()->subMinute())) {
            $error = '系統剛寄出驗證碼，請稍候再試。';
            return false;
        }

        $code = (string) random_int(100000, 999999);

        DB::table('email_verification_codes')->updateOrInsert(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
                'updated_at' => now(),
                'created_at' => $existing?->created_at ?? now(),
            ]
        );

        $mailers = array_unique(array_filter([
            config('mail.default', 'log'),
            'log',
        ]));

        $errors = [];

        foreach ($mailers as $mailer) {
            try {
                Mail::mailer($mailer)->send('emails.verify-code', ['user' => $user, 'code' => $code], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('NHU 二手交易平台 - 註冊驗證碼');
                });

                return true;
            } catch (\Throwable $e) {
                $errors[] = [
                    'mailer' => $mailer,
                    'message' => $e->getMessage(),
                ];
            }
        }

        Log::error('Failed to send verification code email', [
            'email' => $user->email,
            'errors' => $errors,
        ]);

        $error = '寄送驗證碼時發生問題，請稍後再試。';

        return false;
    }
}
