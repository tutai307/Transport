<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Trang chủ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Select2 & Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        /* === Hệ thống Theme (Light/Dark) === */
        :root {
            --body-bg: #fafbfc;
            --sidebar-bg: #f8f9fa;
            --text-color: #333;
            --card-bg: #fff;
            --border-color: #dee2e6;
            --nav-bg: #0d6efd;
            --sidebar-text: #333;
            --sidebar-hover-bg: #0d6efd;
            --sidebar-hover-text: #fff;
        }

        [data-bs-theme="dark"] {
            --body-bg: #1a1d21;
            --sidebar-bg: #212529;
            --text-color: #e9ecef;
            --card-bg: #2c3035;
            --border-color: #495057;
            --nav-bg: #000;
            --sidebar-text: #adb5bd;
            --sidebar-hover-bg: #0d6efd;
            --sidebar-hover-text: #fff;
        }

        /* Override Bootstrap for Dark Mode visibility */
        [data-bs-theme="dark"] .table-primary { --bs-table-bg: #212529; --bs-table-color: #fff; --bs-table-border-color: var(--border-color); }
        [data-bs-theme="dark"] .table-secondary { --bs-table-bg: #2c3035; --bs-table-color: #adb5bd; }
        [data-bs-theme="dark"] .text-success { color: #2ecc71 !important; } /* Sáng hơn trên nền tối */
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
        [data-bs-theme="dark"] .breadcrumb-item.active { color: #adb5bd; }
        [data-bs-theme="dark"] .card-header { background-color: rgba(255,255,255,0.05); }

        /* === UX tối ưu cho người dùng lớn tuổi === */
        body { font-size: 16px; background: var(--body-bg); color: var(--text-color); transition: background 0.3s, color 0.3s; }
        .navbar { font-size: 17px; background: var(--nav-bg) !important; }

        /* Input lớn, dễ chạm */
        .form-control, .form-select {
            font-size: 16px;
            min-height: 46px;
            border-radius: 6px;
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-color);
        }
        .btn {
            font-size: 16px;
            min-height: 44px;
            padding: 8px 20px;
            border-radius: 6px;
        }
        .btn-lg { min-height: 50px; font-size: 17px; }

        /* Bảng dễ đọc */
        .table { font-size: 15px; color: var(--text-color); }
        .table th, .table td { vertical-align: middle; padding: 10px 12px; border-color: var(--border-color); }
        .table th { white-space: nowrap; color: var(--text-color); opacity: 0.8; }

        /* Card */
        .card { background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-color); }
        .card-header { background-color: rgba(0,0,0,0.03); border-color: var(--border-color); }

        /* Label rõ ràng */
        label, .form-label { font-weight: 600; margin-bottom: 4px; font-size: 15px; }

        /* Sidebar */
        .sidebar {
            min-width: 230px;
            max-width: 230px;
            background: var(--sidebar-bg);
            min-height: calc(100vh - 56px);
            border-right: 1px solid var(--border-color);
            transition: background 0.3s;
        }
        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 6px;
            margin: 2px 8px;
            transition: all 0.15s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text);
        }
        .sidebar .nav-link i { width: 24px; display: inline-block; text-align: center; }

        .content-area { flex: 1; min-width: 0; }

        .page-header {
            border-bottom: 2px solid var(--border-color);
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
                background: var(--card-bg);
                border-top: 2px solid var(--border-color);
                z-index: 1000;
                display: flex;
                justify-content: space-around;
                padding: 6px 0;
            }
            .mobile-bottom-nav a {
                text-decoration: none;
                color: var(--sidebar-text);
                font-size: 11px;
                text-align: center;
                padding: 4px 8px;
            }
            .mobile-bottom-nav a.active { color: #0d6efd; }
            .mobile-bottom-nav a i { font-size: 20px; display: block; }
            .content-area { padding-bottom: 70px !important; }
        }
        /* Select2 Bootstrap 5 UX fixes */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 46px;
            padding: 8px 12px;
            font-size: 16px;
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--text-color);
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
                <button class="btn btn-link text-white me-3 p-0" id="theme-toggle" title="Chuyển chế độ sáng/tối">
                    <i class="bi bi-moon-stars fs-5" id="theme-icon"></i>
                </button>
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
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Tổng quan
                </a>
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
        <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*', 'vehicles.*', 'employees.*', 'materials.*', 'routes.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Danh mục
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // --- Dark Mode Logic ---
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        // Tải theme từ localStorage
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', savedTheme);
        updateIcon(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);

            // Thông báo cho các biểu đồ ApexCharts (nếu có)
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
            } else {
                themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
            }
        }

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
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all date pickers with Vietnamese locale
            const dateInputs = document.querySelectorAll('input[type="date"], .date-picker');
            dateInputs.forEach(input => {
                // Change type to text so Flatpickr can style it
                if (input.type === 'date') {
                    input.type = 'text';
                }
                
                flatpickr(input, {
                    locale: 'vn',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    allowInput: true,
                    disableMobile: true,
                    // Large, easy to click UI
                    static: false,
                    appendTo: document.body,
                    defaultDate: input.value || null
                });
            });

            // --- Select2 Initialization ---
            $('.select2').each(function() {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $(this).data('placeholder') || '-- Chọn --',
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "Không tìm thấy kết quả";
                        }
                    }
                });
            });

            // --- Global Bootstrap Validation ---
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
