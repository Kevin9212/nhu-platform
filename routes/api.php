<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\AdminStatsController;
use App\Http\Controllers\Api\AdminAiReportController;

// ping
Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
        'env'     => app()->environment(),
        'time'    => now()->toDateTimeString(),
    ]);
});

// 商品 API
Route::get('/items', [ItemApiController::class, 'index']);
Route::get('/items/{id}', [ItemApiController::class, 'show']);

// 後台統計 API
Route::get('/admin/stats', [AdminStatsController::class, 'index']);

// ⬇⬇⬇ AI 報告 API（**一定要 POST**）
Route::post('/admin/ai-report', AdminAiReportController::class);
// 預設 Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
