<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * 處理搜索請求並顯示結果
     */
    public function index(Request $request){
        // 1. 從請求中取得使用者輸入的關鍵字
        $query = $request->input('q');

        // 2. 在資料庫中執行搜尋
        //    - with() 預先載入圖片和賣家，提升效能
        //    - where() 尋找商品名稱中包含關鍵字的項目
        //    - paginate() 將結果分頁
        $items = IdleItem::with(['images', 'seller'])
            ->where('idle_name', 'LIKE', "%{$query}%")
            ->where('idle_status', 1) // 只搜尋上架中的商品
            ->latest()
            ->paginate(12);

        // 3. 回傳一個新的視圖，並將搜尋結果和關鍵字傳遞過去
        return view('search.results', [
            'items' => $items,
            'query' => $query,
        ]);
    }
}
