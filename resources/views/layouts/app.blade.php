<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Trang chủ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* === UX tối ưu cho người dùng lớn tuổi === */
        body { font-size: 16px; background: #fafbfc; }
        .navbar { font-size: 17px; }

        /* Input lớn, dễ chạm */
        .form-control, .form-select {
            font-size: 16px;
            min-height: 46px;
            border-radius: 6px;
        }
        .btn {
            font-size: 16px;
            min-height: 44px;
            padding: 8px 20px;
            border-radius: 6px;
        }
        .btn-lg { min-height: 50px; font-size: 17px; }

        /* Bảng dễ đọc */
        .table { font-size: 15px; }
        .table th, .table td { vertical-align: middle; padding: 10px 12px; }
        .table th { white-space: nowrap; }

        /* Label rõ ràng */
        label, .form-label { font-weight: 600; margin-bottom: 4px; font-size: 15px; }

        /* Sidebar */
        .sidebar {
            min-width: 230px;
            max-width: 230px;
            background: #f8f9fa;
            min-height: calc(100vh - 56px);
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 6px;
            margin: 2px 8px;
            transition: all 0.15s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }
        .sidebar .nav-link i { width: 24px; display: inline-block; text-align: center; }

        .content-area { flex: 1; min-width: 0; }

        .page-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Alert tự đóng sau 5 giây */
        .alert { font-size: 15px; }

        /* Badge lớn hơn */
        .badge { font-size: 13px; padding: 6px 10px; }

        /* Mobile: bottom nav thay sidebar */
        @media (max-width: 767px) {
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                border-top: 2px solid #dee2e6;
                z-index: 1000;
                display: flex;
                justify-content: space-around;
                padding: 6px 0;
            }
            .mobile-bottom-nav a {
                text-decoration: none;
                color: #666;
                font-size: 11px;
                text-align: center;
                padding: 4px 8px;
            }
            .mobile-bottom-nav a.active { color: #0d6efd; }
            .mobile-bottom-nav a i { font-size: 20px; display: block; }
            .content-area { padding-bottom: 70px !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('trips.index') }}">
                <i class="bi bi-truck"></i> Quản Lý Vận Chuyển
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-sm-inline">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        {{-- Sidebar (Desktop) --}}
        <div class="sidebar d-none d-md-block">
            <nav class="nav flex-column pt-3">
                <a class="nav-link {{ request()->routeIs('trips.*') ? 'active' : '' }}" href="{{ route('trips.index') }}">
                    <i class="bi bi-card-list"></i> Chuyến xe
                </a>
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <i class="bi bi-building"></i> Dự án
                </a>
                <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                    <i class="bi bi-truck-front"></i> Xe
                </a>
                <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                    <i class="bi bi-people"></i> Tài xế
                </a>
                <a class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}" href="{{ route('materials.index') }}">
                    <i class="bi bi-box-seam"></i> Vật liệu
                </a>
                <a class="nav-link {{ request()->routeIs('routes.*') ? 'active' : '' }}" href="{{ route('routes.index') }}">
                    <i class="bi bi-signpost-2"></i> Tuyến đường
                </a>
                <a class="nav-link {{ request()->routeIs('route-materials.*') ? 'active' : '' }}" href="{{ route('route-materials.index') }}">
                    <i class="bi bi-currency-dollar"></i> Bảng giá
                </a>
                <hr class="mx-3">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Báo cáo
                </a>
            </nav>
        </div>

        {{-- Main content --}}
        <div class="content-area p-3 p-md-4">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-success">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Mobile Bottom Nav --}}
    <div class="mobile-bottom-nav d-md-none">
        <a href="{{ route('trips.index') }}" class="{{ request()->routeIs('trips.*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Chuyến xe
        </a>
        <a href="{{ route('trips.create') }}" class="text-success">
            <i class="bi bi-plus-circle-fill"></i> Thêm
        </a>
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-spreadsheet"></i> Báo cáo
        </a>
        <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*', 'vehicles.*', 'employees.*', 'materials.*', 'routes.*', 'route-materials.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Danh mục
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss flash message sau 5 giây
        setTimeout(function() {
            const flash = document.getElementById('flash-success');
            if (flash) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(flash);
                bsAlert.close();
            }
        }, 5000);

        // --- Currency Formatting ---
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            if (value === '') {
                input.value = '';
                return;
            }
            input.value = new Intl.NumberFormat('vi-VN').format(parseInt(value));
        }

        function cleanCurrency(input) {
            return input.value.replace(/\./g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('.currency-input');

            currencyInputs.forEach(input => {
                // Format on load
                formatCurrency(input);

                // Format on input
                input.addEventListener('input', function() {
                    let cursorPosition = this.selectionStart;
                    let oldLength = this.value.length;
                    
                    formatCurrency(this);
                    
                    let newLength = this.value.length;
                    // Adjust cursor position (simple approximation)
                    if (newLength > oldLength) {
                        cursorPosition += (newLength - oldLength);
                    }
                    this.setSelectionRange(cursorPosition, cursorPosition);
                });
            });

            // Strip separators on form submit
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const currencyInputs = form.querySelectorAll('.currency-input');
                    currencyInputs.forEach(input => {
                        input.value = cleanCurrency(input);
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
