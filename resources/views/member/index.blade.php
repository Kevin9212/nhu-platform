{{-- resources/views/member/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員中心 - NHU 二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/member.css') }}">
</head>
<body>

    @include('partials.header')

    <div class="container">
        <div class="member-container">
            {{-- 左側導覽選單 --}}
            <aside class="member-nav">
                <div class="user-profile-summary">
                    <img src="{{ asset($user->avatar ?? 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像') }}" alt="使用者頭像" class="avatar">
                    <p class="nickname">{{ $user->nickname }}</p>
                </div>
                <ul class="nav-list">
                    {{-- 預設將「個人資料」設為活躍分頁 --}}
                    <li><a href="#" data-tab="profile" class="tab-link active">個人資料</a></li>
                    <li><a href="#" data-tab="listings" class="tab-link">我的刊登</a></li>
                    <li><a href="#" data-tab="favorites" class="tab-link">我的收藏</a></li>
                </ul>
            </aside>

            {{-- 右側主要內容 --}}
            <main class="member-content">

                {{-- 個人資料 Tab --}}
                <div id="tab-profile" class="tab-pane active">
                    <section class="section">
                        <h2>個人資料</h2>
                        <p>您可以在這裡更新您的個人資訊。</p>
                        {{-- 載入獨立的個人資料表單檔案 --}}
                        @include('member.partials.profile-form')
                    </section>
                </div>

                {{-- 我的刊登 Tab --}}
                <div id="tab-listings" class="tab-pane">
                    <section class="section">
                        <h2>我的刊登列表</h2>
                        @if($userItems->isNotEmpty())
                        <div class="table-responsive">
                            <table class="listings-table">
                                <thead>
                                    <tr>
                                        <th>圖片</th>
                                        <th>商品名稱</th>
                                        <th>價格</th>
                                        <th>狀態</th>
                                        <th>刊登時間</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userItems as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ $item->images->isNotEmpty() ? asset('storage/' . $item->images->first()->image_url) : 'https://placehold.co/80x80/EFEFEF/AAAAAA&text=無圖片' }}" alt="{{ $item->idle_name }}" class="listing-thumbnail">
                                        </td>
                                        <td data-label="商品名稱"><a href="{{ route('idle-items.show', $item->id) }}">{{ $item->idle_name }}</a></td>
                                        <td data-label="價格">NT$ {{ number_format($item->idle_price) }}</td>
                                        <td data-label="狀態">
                                            @switch($item->idle_status)
                                                @case(1) <span class="status status-active">上架中</span> @break
                                                @case(2) <span class="status status-negotiating">議價中</span> @break
                                                @case(3) <span class="status status-pending">交易中</span> @break
                                                @case(4) <span class="status status-completed">已完成</span> @break
                                                @default <span class="status status-deleted">已刪除</span>
                                            @endswitch
                                        </td>
                                        <td data-label="刊登時間">{{ $item->created_at->format('Y-m-d') }}</td>
                                        <td data-label="操作">
                                            <a href="{{ route('idle-items.edit', $item->id) }}" class="btn btn-sm btn-edit">編輯</a>
                                            <form action="{{ route('idle-items.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('確定要刪除這件商品嗎？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-delete">刪除</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p>您尚未刊登任何商品。</p>
                        @endif
                    </section>
                    <hr style="margin: 2.5rem 0;">
                    <section class="section">
                        <h2>新增商品</h2>
                        {{-- 載入獨立的新增商品表單檔案 --}}
                        @include('idle-items.create-form')
                    </section>
                </div>

                {{-- 我的收藏 Tab --}}
                <div id="tab-favorites" class="tab-pane">
                    <section class="section">
                        <h2>我的收藏</h2>
                        <div class="products">
                            @forelse ($favoriteItems as $favorite)
                                @if($favorite->item) {{-- 確保關聯的商品還存在 --}}
                                <div class="product-card">
                                    <a href="{{ route('idle-items.show', $favorite->item->id) }}" class="product-image-link">
                                        @if($favorite->item->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $favorite->item->images->first()->image_url) }}" alt="{{ $favorite->item->idle_name }}">
                                        @else
                                            <img src="https://placehold.co/600x400/EFEFEF/AAAAAA&text=無圖片" alt="{{ $favorite->item->idle_name }}">
                                        @endif
                                    </a>
                                    <div class="product-content">
                                        <h3><a href="{{ route('idle-items.show', $favorite->item->id) }}">{{ $favorite->item->idle_name }}</a></h3>
                                        <div class="seller">
                                            賣家：<a href="#">{{ $favorite->item->seller->nickname }}</a>
                                        </div>
                                        <p class="price">NT$ {{ number_format($favorite->item->idle_price) }}</p>
                                    </div>
                                </div>
                                @endif
                            @empty
                            <p>您目前沒有任何收藏的商品。</p>
                            @endforelse
                        </div>
                    </section>
                </div>

            </main>
        </div>
    </div>

    <script src="{{ asset('js/member.js') }}"></script>
</body>
</html>
