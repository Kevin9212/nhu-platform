<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $u = $request->user();

        // 1) 未登入
        if (! $u) {
            return $this->deny($request, 401, '請先登入', 'login');
        }

        // 2) 停權（若有此欄位）
        if (($u->is_banned ?? false) === true) {
            return $this->deny($request, 403, '帳號已停權');
        }

        // 3) 角色：只允許 admin / staff（若只允許 admin 就改成嚴格等於 'admin'）
        if (! in_array($u->role ?? 'user', ['admin', 'staff'], true)) {
            return $this->deny($request, 403, '無權限');
        }

        // 4) Email 驗證（User 有 implements MustVerifyEmail 才會生效）
        if ($u instanceof MustVerifyEmail && ! $u->hasVerifiedEmail()) {
            return $this->deny($request, 302, '請先驗證 Email', 'verification.notice');
        }

        // ★ 關鍵：所有檢查都通過時，要回傳後續處理
        return $next($request);
    }

    private function deny(Request $request, int $status, string $message, ?string $redirectRoute = null): Response {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }
        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('error', $message);
        }
        // 這行會丟出例外並結束流程，型別檢查 OK
        abort($status, $message);
    }
}
