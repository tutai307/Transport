@extends('layouts.app')
@section('title', 'Trang chủ')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-speedometer2"></i> Tổng quan hệ thống</h4>
    <div class="text-muted small">Cập nhật: {{ now()->format('d/m/Y H:i') }}</div>
</div>

{{-- Chỉ số tổng quan --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Tổng chuyến xe</h6>
                        <h3 class="mb-0">{{ number_format($totalTrips) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 p-3 rounded-3">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white text-opacity-75 mb-1">Tổng doanh thu</h6>
                        <h3 class="mb-0">{{ number_format($totalRevenue) }} đ</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #198754, #20c997);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 p-3 rounded-3">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white text-opacity-75 mb-1">Tổng lợi nhuận</h6>
                        <h3 class="mb-0">{{ number_format($totalProfit) }} đ</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Biểu đồ xu hướng --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold">Doanh thu & Lợi nhuận (6 tháng gần nhất)</h6>
            </div>
            <div class="card-body">
                <div id="trendChart"></div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ cơ cấu dự án --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold">Tỷ trọng doanh thu theo Dự án</h6>
            </div>
            <div class="card-body">
                <div id="projectChart"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Biểu đồ xu hướng
    var trendOptions = {
        series: [{
            name: 'Doanh thu',
            data: @json($chartRevenue)
        }, {
            name: 'Lợi nhuận',
            data: @json($chartProfit)
        }],
        chart: {
            id: 'trendChart',
            type: 'area',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#0d6efd', '#198754'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($chartMonths),
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return new Intl.NumberFormat('vi-VN').format(value);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return new Intl.NumberFormat('vi-VN').format(value) + " đ";
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            }
        }
    };
    new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

    // 2. Biểu đồ dự án
    var projectOptions = {
        series: @json($projectRevenue),
        chart: {
            id: 'projectChart',
            type: 'donut',
            height: 350
        },
        labels: @json($projectNames),
        colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14'],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return new Intl.NumberFormat('vi-VN').format(value) + " đ";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#projectChart"), projectOptions).render();

    // Lắng nghe sự kiện đổi theme để cập nhật biểu đồ
    window.addEventListener('theme-changed', function(e) {
        const isDark = e.detail.theme === 'dark';
        const themeConfig = {
            theme: { mode: isDark ? 'dark' : 'light' }
        };
        
        ApexCharts.exec('trendChart', 'updateOptions', themeConfig);
        ApexCharts.exec('projectChart', 'updateOptions', themeConfig);
    });
});
</script>
@endpush
@endsection
