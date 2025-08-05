{{-- resources/views/partials/search-form.blade.php --}}
<div class="search-container">
    <form method="GET" action="{{ route('search.index') }}" class="search-form" id="searchForm">
        <div class="search-row">
            <div class="search-input-container">
                <input type="text"
                    name="q"
                    value="{{ $filters['query'] ?? request('q') ?? '' }}"
                    placeholder="搜尋商品名稱或描述..."
                    class="search-input"
                    autocomplete="off"
                    id="searchInput">
                <button type="submit" class="search-button">
                    <span class="search-icon">🔍</span>
                    <span class="search-text">搜尋</span>
                </button>
            </div>
        </div>

        {{-- 進階篩選區塊 --}}
        @if($showAdvanced ?? true)
        <div class="filters-row" id="filtersRow">
            <div class="filter-group">
                <label for="category_id" class="filter-label">分類</label>
                <select name="category_id" id="category_id" class="filter-select">
                    <option value="">所有分類</option>
                    @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ ($filters['category_id'] ?? request('category_id')) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                    @endif
                </select>
            </div>

            <div class="filter-group price-filters">
                <label class="filter-label">價格範圍</label>
                <div class="price-inputs">
                    <input type="number"
                        name="min_price"
                        value="{{ $filters['min_price'] ?? request('min_price') ?? '' }}"
                        placeholder="最低價格"
                        class="price-input"
                        min="0"
                        step="1">
                    <span class="price-separator">-</span>
                    <input type="number"
                        name="max_price"
                        value="{{ $filters['max_price'] ?? request('max_price') ?? '' }}"
                        placeholder="最高價格"
                        class="price-input"
                        min="0"
                        step="1">
                </div>
            </div>

            {{-- 篩選控制按鈕 --}}
            <div class="filter-actions">
                @if(request()->hasAny(['q', 'category_id', 'min_price', 'max_price']))
                <a href="{{ route('search.index') }}" class="clear-filters">清除篩選</a>
                @endif

                @if(!($showAdvanced ?? true))
                <button type="button" class="toggle-filters" onclick="toggleAdvancedFilters()">
                    進階篩選 ▼
                </button>
                @endif
            </div>
        </div>
        @endif
    </form>

    {{-- 搜尋建議下拉選單 --}}
    <div id="searchSuggestions" class="search-suggestions" style="display: none;"></div>
</div>

{{-- 搜尋建議功能 JavaScript --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const suggestionsDiv = document.getElementById('searchSuggestions');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(suggestions => {
                            if (suggestions.length > 0) {
                                suggestionsDiv.innerHTML = suggestions
                                    .map(suggestion => `<div class="suggestion-item" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`)
                                    .join('');
                                suggestionsDiv.style.display = 'block';
                            } else {
                                suggestionsDiv.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('搜尋建議載入失敗:', error);
                            suggestionsDiv.style.display = 'none';
                        });
                }, 300);
            });

            // 點擊其他地方隱藏建議
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.style.display = 'none';
                }
            });
        }
    });

    function selectSuggestion(suggestion) {
        document.getElementById('searchInput').value = suggestion;
        document.getElementById('searchSuggestions').style.display = 'none';
        document.getElementById('searchForm').submit();
    }

    function toggleAdvancedFilters() {
        const filtersRow = document.getElementById('filtersRow');
        const toggleBtn = document.querySelector('.toggle-filters');

        if (filtersRow.style.display === 'none') {
            filtersRow.style.display = 'flex';
            toggleBtn.textContent = '隱藏篩選 ▲';
        } else {
            filtersRow.style.display = 'none';
            toggleBtn.textContent = '進階篩選 ▼';
        }
    }
</script>
@endpush