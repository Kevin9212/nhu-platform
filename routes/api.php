<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\AdminStatsController;

// 🔹 測試 API：確認 api 路由有正常運作
Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
        'env' => app()->environment(),
        'time' => now()->toDateTimeString(),
    ]);
});

// 🔹 商品相關 API
Route::get('/items', [ItemApiController::class, 'index']);
Route::get('/items/{id}', [ItemApiController::class, 'show']);

// 🔹 後台統計 API（開放讀取，給 React 儀表板使用）
Route::get('/admin/stats', [AdminStatsController::class, 'index']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 這裡是 API 路由，會被指派到 "api" middleware group。
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
