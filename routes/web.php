<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdleItemController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\MemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 首頁
Route::get('/', [IdleItemController::class, 'index'])->name('home');

// --- 使用者認證相關 ---
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// --- 忘記密碼相關 ---
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// --- 商品相關 ---
Route::resource('idle-items', IdleItemController::class)->except(['create'])->middleware('auth');

// --- 會員中心相關 ---
Route::get('/member', [MemberController::class, 'index'])->middleware('auth')->name('member.index');
Route::patch('/member/profile', [MemberController::class, 'updateProfile'])->middleware('auth')->name('member.profile.update');

