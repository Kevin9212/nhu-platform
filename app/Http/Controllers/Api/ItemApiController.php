<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdleItem;

class ItemApiController extends Controller
{
    /**
     * 商品列表
     * GET /api/items
     */
    public function index()
    {
        $items = IdleItem::with(['images', 'category', 'seller'])
            ->whereIn('idle_status', [1, 2])
            ->latest('created_at')
            ->take(50)
            ->get()
            ->map(function ($item) {

                $firstImage = $item->images->first();
                $thumbnailUrl = null;

                // 圖片欄位：idle_item_images.image_path
                if ($firstImage && !empty($firstImage->image_path)) {
                    $thumbnailUrl = asset('storage/' . $firstImage->image_path);
                }

                return [
                    'id'            => $item->id,
                    // ✅ 使用實際欄位 idle_name / idle_details / idle_price
                    'title'         => $item->idle_name ?? ('未命名商品 #' . $item->id),
                    'description'   => $item->idle_details,
                    'price'         => $item->idle_price,
                    'category'      => $item->category->name ?? null,
                    // seller 關聯：users.name 或 users.account，都嘗試帶出
                    'seller'        => $item->seller->name
                                        ?? $item->seller->account
                                        ?? null,
                    'thumbnail_url' => $thumbnailUrl,
                    'created_at'    => optional($item->created_at)->toDateTimeString(),
                ];
            });

        return response()->json($items);
    }

    /**
     * 單筆商品詳細
     * GET /api/items/{id}
     */
    public function show($id)
    {
        $item = IdleItem::with(['images', 'category', 'seller'])
            ->findOrFail($id);

        // 只輸出有 image_path 的圖片
        $images = $item->images
            ->filter(function ($img) {
                return !empty($img->image_path);
            })
            ->map(function ($img) {
                return asset('storage/' . $img->image_path);
            })
            ->values();

        return response()->json([
            'id'          => $item->id,
            'title'       => $item->idle_name ?? ('未命名商品 #' . $item->id),
            'description' => $item->idle_details,
            'price'       => $item->idle_price,
            'category'    => $item->category->name ?? null,
            'seller'      => $item->seller->name
                                ?? $item->seller->account
                                ?? null,
            'images'      => $images,
            'created_at'  => optional($item->created_at)->toDateTimeString(),
        ]);
    }
}
