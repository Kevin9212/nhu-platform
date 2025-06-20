<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// 顯示註冊和登入頁面的路由
Route::get('/user', [UserController::class, 'showAuthForm'])->name('user.form');

// 處理註冊請求的路由
Route::post('/user/register', [UserController::class, 'register'])->name('user.register');

// 處理登入請求的路由
Route::post('/user/login', [UserController::class, 'login'])->name('user.login');

// 處理登出請求的路由
Route::get('/user/logout', [UserController::class, 'logout'])->name('user.logout');
