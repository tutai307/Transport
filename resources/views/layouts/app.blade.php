<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>{{ config('app.name') }} - @yield('title', 'Trang chủ')</title>
    <!-- Preconnect to CDNs for faster loading -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">

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
            --body-bg: #121416;
            --sidebar-bg: #1a1d21;
            --text-color: #f8f9fa;
            --card-bg: #1e2125;
            --border-color: #373b3e;
            --nav-bg: #000000;
            --sidebar-text: #dee2e6;
            --sidebar-hover-bg: #0d6efd;
            --sidebar-hover-text: #ffffff;
        }

        /* Override Bootstrap for Dark Mode visibility */
        [data-bs-theme="dark"] .table-primary {
            --bs-table-bg: #1a1d21;
            --bs-table-color: #ffffff;
            --bs-table-border-color: #373b3e;
            --bs-table-striped-bg: #212529;
        }
        [data-bs-theme="dark"] .table-secondary { --bs-table-bg: #2c3035; --bs-table-color: #adb5bd; }
        [data-bs-theme="dark"] .table-warning { --bs-table-bg: #332b00; --bs-table-color: #ffecb5; --bs-table-border-color: #4d4400; }
        [data-bs-theme="dark"] .table-info { --bs-table-bg: #002b33; --bs-table-color: #b5f2ff; }

        [data-bs-theme="dark"] .bg-light { background-color: #212529 !important; color: #f8f9fa !important; }
        [data-bs-theme="dark"] .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; color: #0d6efd !important; }
        [data-bs-theme="dark"] .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }

        [data-bs-theme="dark"] .text-success { color: #4ade80 !important; } /* Sáng hơn (Emerald 400) */
        [data-bs-theme="dark"] .text-danger { color: #f87171 !important; } /* Sáng hơn (Red 400) */
        [data-bs-theme="dark"] .text-warning { color: #fbbf24 !important; } /* Sáng hơn (Amber 400) */
        [data-bs-theme="dark"] .text-info { color: #38bdf8 !important; } /* Sáng hơn (Sky 400) */
        [data-bs-theme="dark"] .text-muted { color: #9ca3af !important; }
        [data-bs-theme="dark"] .breadcrumb-item.active { color: #adb5bd; }
        [data-bs-theme="dark"] .card-header { background-color: rgba(255,255,255,0.03); }

        /* Select2 Dark Mode Fixes */
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        [data-bs-theme="dark"] .select2-dropdown {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: var(--sidebar-hover-bg);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--text-color);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-search__field {
            background-color: var(--body-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }

        /* Flatpickr Dark Mode Fixes */
        [data-bs-theme="dark"] .flatpickr-calendar {
            background: var(--card-bg);
            border-color: var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }
        [data-bs-theme="dark"] .flatpickr-day { color: var(--text-color); }
        [data-bs-theme="dark"] .flatpickr-day:hover { background: var(--sidebar-hover-bg); }
        [data-bs-theme="dark"] .flatpickr-current-month, [data-bs-theme="dark"] .flatpickr-weekday { color: var(--text-color); fill: var(--text-color); }
        [data-bs-theme="dark"] .flatpickr-day.today { border-color: var(--sidebar-hover-bg); }
        [data-bs-theme="dark"] .flatpickr-day.selected { background: var(--sidebar-hover-bg); border-color: var(--sidebar-hover-bg); }

        /* === Breadcrumb responsive === */
        .breadcrumb { font-size: 14px; flex-wrap: nowrap; overflow: hidden; }
        .breadcrumb-item { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .breadcrumb-item.active { color: var(--text-color); }

        @media (max-width: 767px) {
            .breadcrumb { font-size: 13px; }
            /* Ẩn tất cả item trừ 2 cuối */
            .breadcrumb-item:not(:nth-last-child(-n+2)) { display: none; }
            /* Item thứ 2 từ cuối: đổi separator thành mũi tên quay lại */
            .breadcrumb-item:nth-last-child(2)::before { content: "← " !important; }
            .breadcrumb-item { max-width: 55vw; }
        }

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
            .mobile-bottom-nav a i { font-size: 24px; display: block; margin-bottom: 2px; }
            .content-area { padding-bottom: 85px !important; }

            /* FAB - Floating Action Button */
            .fab {
                position: fixed;
                bottom: 85px;
                right: 20px;
                width: 60px;
                height: 60px;
                background-color: #198754;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 1001;
                text-decoration: none;
                transition: transform 0.2s;
            }
            .fab:active { transform: scale(0.9); }
        }

        /* Tối ưu tap targets (ngón tay dễ bấm) */
        @media (max-width: 991px) {
            .form-control, .form-select, .btn, .select2-container--bootstrap-5 .select2-selection {
                font-size: 17px !important;
            }
            .nav-link { padding: 14px 18px !important; }
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
    <script>
        // --- Dark Mode Early Init (Prevent FOUC) ---
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
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
                <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                    <i class="bi bi-calendar-check"></i> Chấm công
                </a>
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <i class="bi bi-building"></i> Dự án
                </a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Xuất hoá đơn
                </a>
                <a class="nav-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}" href="{{ route('payroll.index') }}">
                    <i class="bi bi-wallet2"></i> Tính lương
                </a>

                <hr class="mx-3 my-2">
                <div class="px-3 mb-2 small text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 1px;">Hệ thống</div>

                <a class="nav-link {{ request()->routeIs('vehicles.*', 'employees.*', 'materials.*', 'routes.*') ? 'active' : '' }}"
                   data-bs-toggle="collapse" href="#categoryMenu" role="button" aria-expanded="false">
                    <i class="bi bi-gear"></i> Danh mục <i class="bi bi-chevron-down small ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('vehicles.*', 'employees.*', 'materials.*', 'routes.*') ? 'show' : '' }} ms-3" id="categoryMenu">
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
                </div>
            </nav>
        </div>

        {{-- Main content --}}
        <div class="content-area p-3 p-md-4">
            @yield('content')
        </div>
    </div>

    {{-- Toast container (global notifications) --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index: 1090;"></div>

    {{-- Flash / validation errors bootstrapped to JS on page load --}}
    @php
        $flashPayload = [
            'success' => session('success'),
            'error'   => session('error'),
            'warning' => session('warning'),
            'info'    => session('info'),
            'errors'  => $errors->all(),
        ];
    @endphp
    <script id="flashPayload" type="application/json">{!! json_encode($flashPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    {{-- Mobile Bottom Nav --}}
    <div class="mobile-bottom-nav d-md-none">
        <a href="{{ route('trips.index') }}" class="{{ request()->routeIs('trips.*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Chuyến xe
        </a>
        <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Chấm công
        </a>
        {{-- Nút Thêm gỡ bỏ theo yêu cầu người dùng --}}
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-spreadsheet"></i> Hoá đơn
        </a>
        <a href="{{ route('payroll.index') }}" class="{{ request()->routeIs('payroll.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> Lương
        </a>
        <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index', 'projects.*', 'vehicles.*', 'employees.*', 'materials.*', 'routes.*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Danh mục</span>
        </a>
    </div>

    {{-- Floating Action Button (Mobile Only) --}}
    {{-- FAB gỡ bỏ theo yêu cầu người dùng --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script>
        // --- Dark Mode Logic ---
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        // Tải theme từ localStorage (Cập nhật UI bổ trợ)
        const currentTheme = html.getAttribute('data-bs-theme');
        updateIcon(currentTheme);

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

        // --- Toast notifications (global) ---
        // Sử dụng: window.showToast('Nội dung', 'success'|'error'|'warning'|'info', { delay: 5000 })
        (function() {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const TYPE_MAP = {
                success: { bg: 'text-bg-success', icon: 'bi-check-circle-fill' },
                error:   { bg: 'text-bg-danger',  icon: 'bi-exclamation-circle-fill' },
                danger:  { bg: 'text-bg-danger',  icon: 'bi-exclamation-circle-fill' },
                warning: { bg: 'text-bg-warning', icon: 'bi-exclamation-triangle-fill' },
                info:    { bg: 'text-bg-info',    icon: 'bi-info-circle-fill' },
            };

            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }

            window.showToast = function(message, type, options) {
                if (!message) return;
                const cfg = TYPE_MAP[type] || TYPE_MAP.info;
                const delay = (options && options.delay) || 5000;

                const el = document.createElement('div');
                el.className = `toast align-items-center ${cfg.bg} border-0 shadow`;
                el.setAttribute('role', 'alert');
                el.setAttribute('aria-live', 'assertive');
                el.setAttribute('aria-atomic', 'true');
                el.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ${cfg.icon} me-1"></i> ${escapeHtml(message)}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>`;
                container.appendChild(el);

                const toast = new bootstrap.Toast(el, { delay: delay, autohide: true });
                el.addEventListener('hidden.bs.toast', () => el.remove());
                toast.show();
                return toast;
            };

            // Auto-fire các toast từ server-side flash + validation errors
            const payloadEl = document.getElementById('flashPayload');
            if (!payloadEl) return;
            let payload;
            try { payload = JSON.parse(payloadEl.textContent); } catch (e) { return; }

            if (payload.success) window.showToast(payload.success, 'success');
            if (payload.error)   window.showToast(payload.error,   'error');
            if (payload.warning) window.showToast(payload.warning, 'warning');
            if (payload.info)    window.showToast(payload.info,    'info');
            (payload.errors || []).forEach(msg => window.showToast(msg, 'error'));
        })();

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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js" defer></script>
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
