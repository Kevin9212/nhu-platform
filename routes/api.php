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

// 新增的後台統計 API
Route::get('/admin/stats', [AdminStatsController::class, 'index']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
