<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdleItemController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\PasswordResetController; // 密碼重設
use App\Http\Controllers\NotificationController;       // 通知
use App\Http\Controllers\RatingController;             // 評價
use App\Http\Controllers\NegotiationController;        // 前台議價
use App\Http\Controllers\Admin\DashboardController;    // 後台儀表板
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NegotiationController as AdminNegotiationController;
use App\Http\Controllers\Admin\ItemController; // 後台商品管理


use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Request;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 首頁與核心功能 ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/random-items', [HomeController::class, 'randomItems'])->name('home.random-items');

// --- 使用者認證相關 ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);

    // 忘記密碼
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

/*email驗證路由*/
// 顯示提示頁
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 驗證連結
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/'); // 驗證後導回首頁或會員中心
})->middleware(['auth', 'signed'])->name('verification.verify');

// 重新發送驗證信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '驗證信已寄出！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 登出
Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth');

// --- 賣家個人頁面 ---
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// --- 商品相關 ---
Route::resource('idle-items', IdleItemController::class)->middleware('auth');

// --- 會員中心與收藏 ---
Route::middleware(['auth', 'checkBanned'])->group(function () {
    Route::get('/member', [MemberController::class, 'index'])->name('member.index');
    Route::patch('/member/profile', [MemberController::class, 'updateProfile'])->name('member.profile.update');

    Route::post('/favorites/{idleItem}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{idleItem}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

// --- 搜尋功能 ---
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// --- 聊天室功能 ---
Route::middleware(['auth', 'checkBanned'])->group(function () {
    // 收件匣（顯示所有對話，預設選第一個）
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');

    // 單一聊天室
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');

    // 發送訊息
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])
        ->name('conversations.message.store');
   
// 開始對話    
    Route::get('/conversations/start/{user}', [ConversationController::class, 'start'])
        ->name('conversation.start');
});

// --- 評價功能 ---
Route::middleware(['auth', 'checkBanned'])->group(function () {
    Route::post('/users/{user}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/users/{user}/ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::get('/users/{user}/ratings/summary', [RatingController::class, 'getRatingSummary'])->name('ratings.summary');
});

// --- 驗證碼 ---
Route::get('/captcha', [UserController::class, 'refreshCaptcha'])->name('captcha.refresh');

// --- 通知 ---
Route::middleware(['auth', 'checkBanned'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// --- 後台管理 ---
Route::redirect('/admin', '/admin/dashboard');

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        // 儀表板
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // 使用者管理
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::patch('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');

        // 商品管理
        Route::get('/items', [App\Http\Controllers\Admin\ItemController::class, 'index'])->name('items.index');
        Route::get('/items/{item}', [App\Http\Controllers\Admin\ItemController::class, 'show'])->name('items.show');
        Route::patch('/items/{item}/approve', [App\Http\Controllers\Admin\ItemController::class, 'approve'])->name('items.approve');
        Route::patch('/items/{item}/reject', [App\Http\Controllers\Admin\ItemController::class, 'reject'])->name('items.reject');
        Route::patch('/items/{item}/toggle-status', [App\Http\Controllers\Admin\ItemController::class, 'toggleStatus'])->name('items.toggle');

        // 議價管理
        Route::get('/negotiations', [AdminNegotiationController::class, 'index'])->name('negotiations.index');
        Route::patch('/negotiations/{negotiation}/agree', [AdminNegotiationController::class, 'agree'])->name('negotiations.agree');
        Route::patch('/negotiations/{negotiation}/reject', [AdminNegotiationController::class, 'reject'])->name('negotiations.reject');
    });
Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/negotiations', [NegotiationController::class, 'store'])->name('negotiations.store');
    Route::patch('/negotiations/{negotiation}/agree', [NegotiationController::class, 'agree'])->name('negotiations.agree');
    Route::patch('/negotiations/{negotiation}/reject', [NegotiationController::class, 'reject'])->name('negotiations.reject');
});