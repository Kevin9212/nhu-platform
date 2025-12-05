<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    /** 
     * 顯示忘記密碼的表單頁面
     */
    public function showLinkRequestForm(){
        return view('auth.forgot-password');
    }
    /**
     * 處理忘記密碼的提交，寄送重設密碼的鏈接到Email
     */
    public function sendResetLinkEmail(Request $request){
        $request->validate([
            'account' => 'required|email', // 驗證送過來的 account
        ]);

        // email(account) find user
        $user = User::where('account',$request->account)->first();
        // if user not found,return error
        if(!$user){
            return back()->withErrors(['account' => '找不到該使用者的 email。']);
        }

        if(!$user->hasVerifiedEmail()){
            return back()->withErrors(['account' => '您的信箱尚未完成驗證，請先完成註冊驗證再重設密碼。']);
        }

        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $request->account)
            ->first();

        if ($existingToken && Carbon::parse($existingToken->created_at)->gt(now()->subMinute())) {
            return back()->withErrors(['account' => '重設連結已寄出，請稍候再嘗試。']);
        }
        // 產生Token
        $token = Str::random(60);
        // 儲存Token到Password_reset_token資料庫
        // 如果emial已經存在就更新Token，否則就插入新的記錄
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->account],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        /** 
         * 寄送email的邏輯：
         * 設定MAIL_MAILER=log,所以内容會寫入log檔案
        */
        //Log::info("Password reset link for {$user->account}:" . url(route('password.reset', ['token' => $token, 'email' => $user->account], false)));
        
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->account], false));

        $mailers = array_unique(array_filter([
            config('mail.default', 'log'),
            'log',
        ]));

        $sent = false;
        $errors = [];

        foreach ($mailers as $mailer) {
            try {
                Mail::mailer($mailer)->send('emails.password-reset', ['user' => $user, 'resetUrl' => $resetUrl], function ($message) use ($user) {
                    $message->to($user->account)
                        ->subject('NHU 二手交易平台 - 密碼重設通知');
                });

                $sent = true;
                break;
            } catch (\Throwable $e) {
                $errors[] = [
                    'mailer' => $mailer,
                    'message' => $e->getMessage(),
                ];
            }
        }

        if (!$sent) {
            Log::error('Failed to send password reset email', [
                'email' => $user->account,
                'error' => $e->errors,
            ]);

            return back()->withErrors(['account' => '寄送密碼重設郵件時發生錯誤，請稍後再試。']);
        }

        Log::info("Password reset link for {$user->account}:" . $resetUrl);
            return back()->with('status','重設密碼的鏈接已經發送到您的Email。請檢查您的郵箱。');
    }

    /**
     * 顯示重設密碼的頁面
     */
    public function showResetForm(Request $request,$token = null){
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * 重設使用者密碼的提交
     */
    public function reset(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed', // 密碼欄位是必須的，最小長度為6個字符，並且需要確認密碼
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
        

        $expiresAt = Carbon::parse(optional($resetRecord)->created_at)->addMinutes(
            config('auth.passwords.users.expire', 60)
        );

        // 如果找不到記錄、過期，或者Token不匹配，則返回錯誤
        if(!$resetRecord || !Hash::check($request->token,$resetRecord->token) || now()->greaterThan($expiresAt)){
            return back()->withInput($request->only('email'))
                ->withErrors(['token' => '重設密碼連結已失效，請重新申請。']);
        }

        $user = User::where('account', $request->email)->first();
        if(!$user){
            return back()->withInput($request->only('email'))
            ->withErrors(['email' => '找不到該使用者的Email']);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        // 刪除重設密碼的Token
        DB::table('password_reset_tokens')
            ->where('email',$request->email)
            ->delete();
            return redirect()->route('login')
                ->with('status', '密碼已經重設成功，請登入');
    }
}
