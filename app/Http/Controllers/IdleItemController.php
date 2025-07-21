<?php

namespace App\Http\Controllers;

use App\Models\IdleItem;
use App\Models\Category;
use App\Models\ProductImage;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdleItemController extends Controller {
    /**
     * 顯示主頁與商品列表
     */
    public function index() {
        // 爲了讓關聯的圖片和賣家的資訊可以一起載入，用with() 來預設
        // N+1 問題的解決方案
        $items = IdleItem::with(['images', 'seller'])
            ->where('idle_status', 1) // 只顯示上架的商品
            ->orderBy('release_time', 'desc') // 按照上架時間降序排列
            ->paginate(12); // 分頁，每頁顯示12個商品

        $categories = Category::all(); // 獲取所有商品分類

        // 回傳商品列表視圖，並傳遞商品資料
        return view('home', ['items' => $items, 'categories' => $categories]);
    }

    
    /**
     * 顯示商品新增表單
     */
    /*
    public function create() {
        // 取得所有商品分類
        $categories = Category::all();
        return view('idle-items.create', ['categories' => $categories]);
    }
    */
    /**
     *  把新增加的商品存入資料庫
     */
    public function store(Request $request) {
        // 1.驗證請求資料
        $validated = $request->validate([
            'idle_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'idle_price' => 'required|numeric|min:0',
            'idle_details' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 驗證圖片
        ]);

        // 2.圖片上傳
        $imagePath = null;
        if ($request->hasFile('image')) {
            // a.處理上傳圖片
            $imageFile = $request->file('image');
            // b.生成唯一的檔案名
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            // c.把相片移動到public/images/products目錄
            $imageFile->move(public_path('images/products'), $imageName);
            // d.準備好要存入的這兩款的圖片路徑
            $imagePath = 'images/products/' . $imageName;
        }

        // 3.建立商品資料
        $item = new IdleItem();
        $item->user_id = Auth::id(); // 設定賣家ID
        $item->category_id = $validated['category_id']; // 設定商品分類ID
        $item->idle_name = $validated['idle_name']; // 設定商品名稱
        $item->idle_details = $validated['idle_details']; // 設定商品描述
        $item->idle_price = $validated['idle_price']; // 設定商品價格
        $item->release_time = now(); // 設定上架時間
        $item->idle_status = 1; // 設定商品狀態為
        $item->save(); // 先儲存商品，取得商品id

        // 4.如果有上傳圖片，就建立商品圖片資料
        if ($imagePath) {
            $productImage = new ProductImage();
            $productImage->idle_item_id = $item->id; // 設定商品ID
            $productImage->image_url = $imagePath; // 設定圖片路徑
            $productImage->save(); // 儲存商品圖片資料
        }

        // 5.從新導向首頁，附帶成功的訊息
        return redirect()->route('home')->with('success', '商品已成功刊登！');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
