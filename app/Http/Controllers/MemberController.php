<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Negotiation;
use App\Models\Order;
use App\Models\Rating;

class MemberController extends Controller {
    /**
     * 顯示會員中心頁面。
     */
    public function index(Request $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 取得使用者刊登的商品
        $userItems = $user->items()->with('images')->latest()->get();
        // 取得使用者收藏的商品
        $favoriteItems = $user->favorites()->with('item.images')->latest()->get();
        // 取得所有分類，供「新增商品」表單使用
        $categories = Category::all();

        // 核心功能：取得該使用者的所有對話
        // 無論是作為買家還是賣家，都撈取出來
        $conversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with([
                'buyer', // 預先載入買家資訊
                'seller', // 預先載入賣家資訊
                'messages' => function ($query) {
                    // 只載入最新的一則訊息，用來當作預覽
                    $query->latest()->limit(1);
                }
            ])
            ->latest('updated_at') // 讓有最新訊息的對話排在最上面
            ->get();


        // 賣家議價整合： 直接拉出賣家所有議價記錄，含商品封面，買家訊息
        $negotiations = Negotiation::where('seller_id', $user->id)
            ->with([
                'item.images',
                'buyer'
            ])
            ->latest('updated_at')
            ->get();

        $groupedNegotiations = $negotiations
            ->groupBy('idle_item_id')
            ->map(fn ($group) => $group->sortBy('price')->values());

        // 直接映射買家/商品對飲的對話id，翻遍檢視1對1
        $conversationLookup = Conversation::where('seller_id', $user->id)
            ->whereIn('idle_item_id', $negotiations->pluck('idle_item_id'))
            ->get()
            ->keyBy(fn ($c) => $c->buyer_id . '-' . $c->idle_item_id);
        
        // 買家議價整合：讓買家也能看到自己和賣家的議價狀態
        $buyerNegotiations = Negotiation::where('buyer_id', $user->id)
            ->with([
                'item.images',
                'seller'
            ])
            ->latest('updated_at')
            ->get();

        $buyerNegotiationOrders = Order::with(['item.images', 'item.user', 'user'])
            ->where('user_id', $user->id)
            ->whereIn('idle_item_id', $buyerNegotiations->pluck('idle_item_id'))
            ->latest('updated_at')
            ->get();

        $buyerConversationLookup = Conversation::where('buyer_id', $user->id)
            ->whereIn('idle_item_id', $buyerNegotiations->pluck('idle_item_id'))
            ->get()
            ->keyBy(fn ($c) => $c->seller_id . '-' . $c->idle_item_id);
        
        $hasAcceptedNegotiation = $groupedNegotiations->flatten()->contains('status', 'accepted')
            || $buyerNegotiations->contains('status', 'accepted');

        if ($request->query('tab') === 'orders' && ! $hasAcceptedNegotiation) {
            return redirect()
                ->to(route('member.index', ['tab' => 'negotiations']) . '#negotiations')
                ->with('error', '此議價尚未由賣家接受，請先回到議價總覽');
        }
        // 購買與販售的訂單
        $buyerOrders = Order::with(['item.images', 'item.user', 'user'])
            ->where('user_id', $user->id)
            ->whereHas('item.negotiations', function ($query) use ($user) {
                $query->whereColumn('negotiations.buyer_id', 'orders.user_id')
                    ->where('status', 'accepted');
            })
            ->latest('updated_at')
            ->get();

        $sellingItemIds = $groupedNegotiations->keys();

        $sellerNegotiationOrders = Order::with(['item.images', 'item.user', 'user'])
            ->whereHas('item', fn ($query) => $query->where('user_id', $user->id))
            ->whereIn('idle_item_id', $sellingItemIds)
            ->latest('updated_at')
            ->get();

        $sellerOrders = Order::with(['item.images', 'item.user', 'user'])
            ->whereHas('item', fn ($query) => $query->where('user_id', $user->id))
            ->whereHas('item.negotiations', function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->whereColumn('negotiations.buyer_id', 'orders.user_id')
                    ->where('status', 'accepted');
            })
            ->latest('updated_at')
            ->get();

            $buyerRatings = Rating::where('rater_id', $user->id)
            ->whereIn('order_id', $buyerOrders->pluck('id'))
            ->get()
            ->keyBy('order_id');

        return view('member.index', [
            'user' => $user,
            'favoriteItems' => $favoriteItems,
            'userItems' => $userItems,
            'categories' => $categories,
            'conversations' => $conversations, // 將對話資料傳遞給視圖
            'negotiations' => $negotiations,
            'groupedNegotiations' => $groupedNegotiations,
            'conversationLookup' => $conversationLookup,
            'buyerNegotiations' => $buyerNegotiations,
            'buyerConversationLookup' => $buyerConversationLookup,
            'buyerNegotiationOrders' => $buyerNegotiationOrders,
            'buyerOrders' => $buyerOrders,
            'sellerNegotiationOrders' => $sellerNegotiationOrders,
            'sellerOrders' => $sellerOrders,
            'buyerRatings' => $buyerRatings,
            'hasAcceptedNegotiation' => $hasAcceptedNegotiation,
        ]);
    }

    /**
     * 更新使用者的個人資料。
     */
    public function updateProfile(Request $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nickname' => 'required|string|max:32',
            'user_phone' => 'required|string|max:16',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->nickname = $validated['nickname'];
        $user->user_phone = $validated['user_phone'];

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = $user->id . '_' . uniqid() . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->storeAs('avatars', $avatarName, 'public');
            $user->avatar = 'avatars/' . $avatarName;
        }

        $user->save();

        return back()->with('profile_success', '您的個人資料已成功更新！');
    }
}
