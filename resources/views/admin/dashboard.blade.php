    {{-- resources/views/admin/dashboard.blade.php --}}
    @extends('layouts.admin')

    @section('title', '儀表板')

    @section('content')
    <h1>儀表板</h1>
    <p>歡迎來到管理後台！</p>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>總註冊用戶</h4>
            <p>{{ $stats['total_users'] }}</p>
        </div>
        <div class="stat-card">
            <h4>總刊登商品</h4>
            <p>{{ $stats['total_items'] }}</p>
        </div>
        <div class="stat-card">
            <h4>目前上架商品</h4>
            <p>{{ $stats['active_items'] }}</p>
        </div>
    </div>
    @endsection

    @push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-card h4 {
            margin-top: 0;
            color: #6c757d;
        }

        .stat-card p {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0;
        }
    </style>
    @endpush