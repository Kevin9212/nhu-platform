{{-- resources/views/member/partials/listings-table.blade.php --}}

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