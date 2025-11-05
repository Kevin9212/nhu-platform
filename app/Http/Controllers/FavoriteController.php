<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\IdleItem;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, IdleItem $idleItem)
    {
        // 若不允許收藏自己的商品，可保留這段
        if ($request->user()->id === ($idleItem->seller_id ?? optional($idleItem->seller)->id)) {
            return redirect($request->input('redirect_to', url()->previous()))
                ->with('error', '不能收藏自己的商品');
        }

        Favorite::firstOrCreate([
            'user_id'      => $request->user()->id,
            'idle_item_id' => $idleItem->id,   // ✅ 對應 migration
        ]);

        return redirect($request->input('redirect_to', url()->previous()))
            ->with('success', '已加入收藏');
    }

    public function destroy(Request $request, IdleItem $idleItem)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('idle_item_id', $idleItem->id) // ✅ 對應 migration
            ->delete();

        return redirect($request->input('redirect_to', url()->previous()))
            ->with('success', '已取消收藏');
    }
}
