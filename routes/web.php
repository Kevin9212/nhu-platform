<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdleItemController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\PasswordResetController; // 新增：引入密碼重設控制器

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 首頁與核心功能 ---
// 修正：首頁應由 HomeController 處理
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/random-items', [HomeController::class, 'randomItems'])->name('home.random-items');

// --- 使用者認證相關 ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);

    // 忘記密碼相關
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// 登出路由需要使用者在登入狀態
Route::post('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth');

// --- 賣家個人頁面 ---
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// --- 商品相關 (使用 resource controller) ---
// 修正：移除 except(['create']) 以便 'idle-items.create' 路由存在
Route::resource('idle-items', IdleItemController::class)->middleware('auth');

// --- 會員中心與收藏 ---
Route::middleware('auth')->group(function () {
    Route::get('/member', [MemberController::class, 'index'])->name('member.index');
    Route::patch('/member/profile', [MemberController::class, 'updateProfile'])->name('member.profile.update');
    // 收藏功能路由
    Route::post('/favorites/{idleItem}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{idleItem}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

// --- 搜尋功能 ---
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// --- 聊天功能 ---
Route::middleware('auth')->group(function () {
    Route::get('/conversation/with/{user}', [ConversationController::class, 'startOrShow'])->name('conversation.start');
    Route::post('/conversation/{conversation}/messages', [ConversationController::class, 'storeMessage'])->name('conversation.message.store');
});
