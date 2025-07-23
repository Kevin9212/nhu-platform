<?php

namespace App\Http\Controllers;

// 引入 Laravel 基礎 Controller，這是所有 Controller 的父類別
use App\Http\Controllers\Controller;
// 引入權限檢查功能所需的 Trait
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// 引入會用到的 Model
use App\Models\Category;
use App\Models\IdleItem;

// 引入 Laravel 的核心功能類別
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdleItemController extends Controller
{
    // 使用這個 Trait 來啟用 $this->authorize() 方法
    use AuthorizesRequests;

    /**
     * 顯示主頁與商品列表
     */
    public function index()
    {
        $items = IdleItem::with(['images', 'seller'])
            ->where('idle_status', 1)
            ->latest()
            ->paginate(12);

        $randomItems = IdleItem::with(['images', 'seller'])
            ->where('idle_status', 1)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $categories = Category::all();

        return view('home', [
            'items' => $items,
            'randomItems' => $randomItems,
            'categories' => $categories,
        ]);
    }

    /**
     * 將新增加的商品存入資料庫
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idle_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'idle_price' => 'required|numeric|min:0',
            'idle_details' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 透過 user 的 items() 關聯來建立新商品，這會自動填入 user_id
        $item = $user->items()->create([
            'category_id' => $validated['category_id'],
            'idle_name' => $validated['idle_name'],
            'idle_details' => $validated['idle_details'],
            'idle_price' => $validated['idle_price'],
            'idle_status' => 1,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('products', 'public');
                $item->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('member.index')->with('success', '商品已成功刊登！');
    }

    /**
     * 顯示指定的單一商品資源
     */
    public function show(IdleItem $item)
    {
        $item->load(['seller', 'images']);
        return view('idle-items.show', ['item' => $item]);
    }

    /**
     * 顯示編輯商品的表單
     */
    public function edit(IdleItem $item)
    {
        $this->authorize('update', $item);

        $categories = Category::all();
        return view('idle-items.edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    /**
     * 更新指定的商品資源
     */
    public function update(Request $request, IdleItem $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'idle_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'idle_price' => 'required|numeric|min:0',
            'idle_details' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item->update($validated);

        if ($request->hasFile('images')) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_url);
            }
            $item->images()->delete();
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('products', 'public');
                $item->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('member.index')->with('success', '商品已成功更新！');
    }

    /**
     * 刪除指定的商品資源
     */
    public function destroy(IdleItem $item)
    {
        $this->authorize('delete', $item);

        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }

        $item->delete();

        return redirect()->route('member.index')->with('success', '商品已成功刪除！');
    }
}
