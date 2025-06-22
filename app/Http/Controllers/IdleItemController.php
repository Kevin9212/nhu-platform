<?php

namespace App\Http\Controllers;

use App\Models\IdleItem;
use Illuminate\Http\Request;

class IdleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //從資料庫中，找出上架的商品(idle_status = 1)的商品
        $items = IdleItem::where('idle_status', 1)
            ->orderBy('release_time', 'desc') // 按照上架時間降序排列
            ->paginate(10); // 分頁，每頁顯示10個商品
        
        // 回傳商品列表視圖，並傳遞商品資料
        return view('home',['items' => $items]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
