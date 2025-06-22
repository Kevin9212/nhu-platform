<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdleItemController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', [IdleItemController::class, 'index'])->name('home');

// 顯示登入頁面
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
// 處理登入請求
Route::post('/login', [UserController::class, 'login']);

// 顯示註冊頁面
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
// 處理註冊請求
Route::post('/register', [UserController::class, 'register']);

// 處理登出請求
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// 顯示忘記密碼頁面
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request'); // 顯示忘記密碼的表單頁面

// 處理忘記密碼的提交，寄從設鏈接Email
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email'); // 發送重設密碼的郵件

// 顯示重設密碼的頁面（從email點鏈接到這裏）
Route::get('\reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');  // 顯示重設密碼的表單頁面

// 處理重設密碼的提交更新密碼
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest') // 這裏的middleware是guest，表示只有未登入的使用者可以訪問
    ->name('password.update'); // 更新密碼
