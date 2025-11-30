<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdleItemController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\PasswordResetController; // 密碼重設
use App\Http\Controllers\NotificationController;       // 通知
use App\Http\Controllers\RatingController;             // 評價
use App\Http\Controllers\NegotiationController;        // 前台議價
use App\Http\Controllers\Admin\DashboardController;    // 後台儀表板
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NegotiationController as AdminNegotiationController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// --- 首頁與核心功能 ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/random-items', [HomeController::class, 'randomItems'])->name('home.random-items');

// --- 使用者認證相關（訪客可見） ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');          // ✔ login
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');

    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register'); // ✔ register
    Route::post('/register', [UserController::class, 'register'])->name('register.submit');

    // 忘記密碼
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// email 驗證路由
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/'); // 驗證後導回首頁
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '驗證信已寄出！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 登出（登入者可見）
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
    // 收件匣（顯示所有對話）
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    // 單一聊天室
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    // 發送訊息
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])
        ->name('conversations.message.store');
    // 開始對話
    Route::get('/conversations/start/{user}', [ConversationController::class, 'start'])->name('conversation.start');
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
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index'); // 頁面
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read'); // 單筆已讀
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll'); // ✅ 全部已讀（新增）
    Route::get('/notifications/fetch-unread', [NotificationController::class, 'fetchUnread'])->name('notifications.fetch'); // 鈴鐺 AJAX
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
        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/{item}', [AdminItemController::class, 'show'])->name('items.show');
        Route::patch('/items/{item}/approve', [AdminItemController::class, 'approve'])->name('items.approve');
        Route::patch('/items/{item}/reject', [AdminItemController::class, 'reject'])->name('items.reject');
        Route::patch('/items/{item}/toggle-status', [AdminItemController::class, 'toggleStatus'])->name('items.toggle');

        // 議價管理
        Route::get('/negotiations', [AdminNegotiationController::class, 'index'])->name('negotiations.index');
        Route::patch('/negotiations/{negotiation}/agree', [AdminNegotiationController::class, 'agree'])->name('negotiations.agree');
        Route::patch('/negotiations/{negotiation}/reject', [AdminNegotiationController::class, 'reject'])->name('negotiations.reject');
    });

// 前台議價（登入者）
Route::middleware(['auth'])->group(function () {
    Route::post('/items/{item}/negotiations', [NegotiationController::class, 'store'])->name('negotiations.store');
    Route::patch('/negotiations/{negotiation}/agree', [NegotiationController::class, 'agree'])->name('negotiations.agree');
    Route::patch('/negotiations/{negotiation}/reject', [NegotiationController::class, 'reject'])->name('negotiations.reject');
    Route::post('/negotiations/{negotiation}/to-orders', [NegotiationController::class, 'redirectToOrders'])->name('negotiations.to-orders');
});

// React Admin Dashboard
Route::get('/react-admin/{any?}', function () {
    return file_get_contents(public_path('react-admin/index.html'));
})->where('any', '.*');