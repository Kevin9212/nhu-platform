<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        if (Auth::check()) {
            $user = Auth::user();

            // 如果帳號被標記為 banned
            if ($user->user_status === 'banned') {
                // 檢查是否設定了 banned_until
                if ($user->banned_until && now()->greaterThan($user->banned_until)) {
                    // ★ 封禁已過期 → 自動解封
                    $user->user_status = 'active';
                    $user->banned_until = null;
                    $user->save();
                } else {
                    // 還在封禁期間 → 強制登出
                    Auth::logout();
                    return redirect()->route('login')
                        ->withErrors([
                            'email' => '您的帳號已被封禁，直到 ' . optional($user->banned_until)->format('Y-m-d H:i')
                        ]);
                }
            }
        }

        return $next($request);
    }
}
