{{-- resources/views/member/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員中心 - NHU 二手交易平台</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/member.css') }}">
</head>

<body>

    @include('partials.header')

    <div class="container">
        <div class="member-container">
            {{-- 左側導覽選單 --}}
            <aside class="member-nav">
                <ul class="nav-list">
                    <li><a href="#" data-tab="favorites" class="tab-link">我的收藏</a></li>
                    @auth
                    <li><a href="#" data-tab="listings" class="tab-link">我的刊登</a></li>
                    {{-- 將 active class 移到這裡 --}}
                    <li><a href="#" data-tab="profile" class="tab-link active">個人資料</a></li>
                    @endauth
                </ul>
            </aside>

            {{-- 右側主要內容 --}}
            <main class="member-content">
                {{-- 我的收藏 Tab --}}
                <div id="tab-favorites" class="tab-pane">
                    <section class="section">
                        <h2>我的收藏</h2>
                        <div class="products">
                            @forelse ($favoriteItems as $item)
                            <div class="product-card">
                                {{-- 商品卡片內容... --}}
                            </div>
                            @empty
                            <p>您目前沒有任何收藏的商品。</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                @auth
                {{-- 我的刊登 Tab --}}
                <div id="tab-listings" class="tab-pane">
                    <section class="section">
                        <h2>我的刊登列表</h2>
                        @if($userItems->isNotEmpty())
                        <table class="listings-table">
                            {{-- 表格內容... --}}
                        </table>
                        @else
                        <p>您尚未刊登任何商品。</p>
                        @endif
                    </section>
                    <hr style="margin: 2.5rem 0;">
                    <section class="section">
                        <h2>新增商品</h2>
                        @include('idle-items.create-form')
                    </section>
                </div>

                {{-- 個人資料 Tab --}}
                <div id="tab-profile" class="tab-pane active">
                    <section class="section">
                        <h2>個人資料</h2>
                        <p>您可以在這裡更新您的個人資訊。</p>

                        {{-- 直接載入獨立的個人資料表單檔案 --}}
                        @include('member.partials.profile-form')

                    </section>
                </div>
                @endauth
            </main>
        </div>
    </div>

    <script src="{{ asset('js/member.js') }}"></script>
</body>

</html>