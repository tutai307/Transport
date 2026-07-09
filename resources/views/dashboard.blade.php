@extends('layouts.app')
@section('title', 'Trang chủ')

@section('content')
<div class="page-header mb-4">
    <div class="row align-items-center g-2">
        <div class="col">
            <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-speedometer2"></i> Tổng quan hệ thống</h4>
        </div>
        <div class="col-auto">
            <div class="text-muted small"><i class="bi bi-clock"></i> {{ now()->format('H:i d/m/y') }}</div>
        </div>
    </div>
</div>

{{-- Chỉ số tổng quan --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Tổng số chuyến</h6>
                        <h3 class="mb-0">{{ intval($totalTrips) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 p-3 rounded-3">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-white text-opacity-75 mb-1">Tổng tiền cước</h6>
                        <h3 class="mb-0">{{ number_format($totalFreightAmount) }} đ</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($tripsWithoutDriverCount > 0)
<div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
    <div class="card-body">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3">
                <i class="bi bi-person-x fs-3"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="mb-1 fw-bold">
                    Có {{ $tripsWithoutDriverCount }} chuyến xe chưa gán tài xế
                </h6>
                <p class="text-muted small mb-2">Bổ sung tài xế để đảm bảo tính lương và đối soát đầy đủ.</p>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Ngày</th>
                                <th>Dự án</th>
                                <th>Xe</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tripsWithoutDriver as $trip)
                                <tr>
                                    <td class="text-nowrap">{{ $trip->trip_date->format('d/m/Y') }}</td>
                                    <td>{{ optional($trip->project)->name ?? '—' }}</td>
                                    <td>{{ $trip->vehicle_plate_snapshot ?? optional($trip->vehicle)->plate_number ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-warning" title="Bổ sung tài xế">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($tripsWithoutDriverCount > $tripsWithoutDriver->count())
                    <small class="text-muted">Và {{ $tripsWithoutDriverCount - $tripsWithoutDriver->count() }} chuyến khác...</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Biểu đồ xu hướng tiền cước --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-0">
                <h6 class="mb-0 fw-bold">Tiền cước & Số chuyến (6 tháng gần nhất)</h6>
            </div>
            <div class="card-body">
                <div id="trendChart"></div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ số chuyến xe theo dự án --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-0">
                <h6 class="mb-0 fw-bold">Số chuyến xe theo Dự án</h6>
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
    // 1. Biểu đồ xu hướng tiền cước + số chuyến
    var trendOptions = {
        series: [
            {
                name: 'Tiền cước (đ)',
                type: 'area',
                data: @json($chartFreight)
            },
            {
                name: 'Số chuyến',
                type: 'line',
                data: @json($chartTrips)
            }
        ],
        chart: {
            id: 'trendChart',
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#0d6efd', '#20c997'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: [3, 2] },
        xaxis: {
            categories: @json($chartMonths),
        },
        yaxis: [
            {
                title: { text: 'Tiền cước (đ)' },
                labels: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('vi-VN').format(value);
                    }
                }
            },
            {
                opposite: true,
                title: { text: 'Số chuyến' },
                labels: {
                    formatter: function (value) {
                        return value;
                    }
                }
            }
        ],
        tooltip: {
            y: [
                {
                    formatter: function (value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + " đ";
                    }
                },
                {
                    formatter: function (value) {
                        return value + " chuyến";
                    }
                }
            ]
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            }
        }
    };
    new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

    // 2. Biểu đồ số chuyến xe theo dự án
    var projectOptions = {
        series: @json($projectTrips),
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
                    return value + " chuyến";
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
