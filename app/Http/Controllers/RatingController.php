<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    /**
     * 儲存使用者的新評價
     */
    public function store(Request $request, User $user){
        // 驗證輸入資料
        $validated = $request->validate([
            'score' =>'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        /**
         * @var \App\Models\User $rater
         */
        $rater = Auth::user();

        // 防護機制：使用者不能評價自己
        if ($rater->id === $user->id) {
            return back()->with('error', '您不能評價自己。');
        }

        // 防護機制：檢查是否已經評價過此使用者
        $existingRating = Rating::where('rater_id', $rater->id)
            ->where('rated_id', $user->id)
            ->first();

        if ($existingRating) {
            return back()->with('error', '您已經評價過此使用者了。');
        }

        // 建立新的評價
        Rating::create([
            'rater_id' => $rater->id, // 評分者是當前登入的使用者
            'rated_id' => $user->id,  // 被評分者是當前頁面的使用者
            'score' => $validated['score'],
            'comment' => $validated['comment'],
            // 注意：我們暫時簡化，沒有將評價與特定訂單 (order_id) 關聯
        ]);

        return back()->with('success', '感謝您的評價！');
    }

    /**
     * 更新現有評價（可選功能）
     */
    public function update(Request $request, User $user, Rating $rating) {
        // 確認只有評價的作者可以修改
        if ($rating->rater_id !== Auth::id()) {
            return back()->with('error', '您沒有權限修改此評價。');
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating->update($validated);

        return back()->with('success', '評價已更新。');
    }

    /**
     * 刪除評價（可選功能）
     */
    public function destroy(User $user, Rating $rating) {
        // 確認只有評價的作者可以刪除
        if ($rating->rater_id !== Auth::id()) {
            return back()->with('error', '您沒有權限刪除此評價。');
        }

        $rating->delete();

        return back()->with('success', '評價已刪除。');
    }

    /**
     * 顯示某個使用者收到的所有評價
     */
    public function index(User $user) {
        $ratings = Rating::where('rated_id', $user->id)
            ->with('rater') // 預載入評分者資訊
            ->latest()
            ->paginate(10);

        // 計算平均分數
        $averageRating = $ratings->avg('score');
        $totalRatings = $ratings->total();

        return view('ratings.index', compact('user', 'ratings', 'averageRating', 'totalRatings'));
    }

    /**
     * AJAX：取得使用者評價摘要（用於動態載入）
     */
    public function getRatingSummary(User $user) {
        $ratings = Rating::where('rated_id', $user->id);

        $summary = [
            'average' => round($ratings->avg('score'), 1),
            'total' => $ratings->count(),
            'distribution' => [
                5 => $ratings->where('score', 5)->count(),
                4 => $ratings->where('score', 4)->count(),
                3 => $ratings->where('score', 3)->count(),
                2 => $ratings->where('score', 2)->count(),
                1 => $ratings->where('score', 1)->count(),
            ]
        ];

        return response()->json($summary);
    }
}