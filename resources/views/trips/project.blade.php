@extends('layouts.app')
@section('title', $project->name . ' - Theo tháng')

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Chuyến xe</a></li>
            <li class="breadcrumb-item active">{{ $project->name }}</li>
        </ol>
    </nav>
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md">
            <h4 class="mb-0 text-break"><i class="bi bi-building"></i> {{ $project->name }}</h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2">
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['project_id' => $project->id, 'export_type' => 'freight']) }}">
                            <i class="bi bi-currency-dollar"></i> Xuất theo giá cước
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['project_id' => $project->id, 'export_type' => 'profit']) }}">
                            <i class="bi bi-graph-up"></i> Xuất theo lợi nhuận
                        </a></li>
                    </ul>
                </div>
                <a href="{{ route('trips.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-plus-circle"></i> Thêm chuyến
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tổng kết dự án --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-6">
        <div class="card border-primary h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0 fw-bold">{{ $projectSummary['total_trips'] + 0 }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="card border-success h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng tiền</small>
                <h5 class="text-success mb-0 fw-bold">{{ number_format($projectSummary['total_price'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
</div>

{{-- Biểu đồ chi tiết dự án --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-0 bg-transparent">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-primary"></i> Biểu đồ số chuyến theo ngày</h6>
            </div>
            <div class="card-body">
                <div id="dailyVolumeChart"></div>
            </div>
        </div>
    </div>
</div>

{{-- Danh sách tháng --}}
<div class="row g-3">
    @forelse($months as $m)
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('trips.by-month', ['project' => $project->id, 'year' => $m->year, 'month' => $m->month]) }}"
               class="text-decoration-none">
                <div class="card shadow-sm hover-card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="bi bi-calendar-month"></i>
                            Tháng {{ $m->month }}/{{ $m->year }}
                        </h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-muted small">Chuyến</div>
                                <div class="fw-bold fs-5">{{ $m->trip_count + 0 }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Tiền</div>
                                <div class="fw-bold text-success">{{ number_format($m->total_price, 0, ',', '.') }}đ</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <a href="{{ route('reports.export', ['project_id' => $project->id, 'year' => $m->year, 'month' => $m->month, 'export_type' => 'freight']) }}" 
                               class="btn btn-sm btn-outline-success" title="Xuất Cước tháng này">
                                <i class="bi bi-file-earmark-excel"></i> Cước
                            </a>
                            <a href="{{ route('reports.export', ['project_id' => $project->id, 'year' => $m->year, 'month' => $m->month, 'export_type' => 'profit']) }}" 
                               class="btn btn-sm btn-outline-warning" title="Xuất Lợi Nhuận tháng này">
                                <i class="bi bi-file-earmark-excel"></i> Lãi
                            </a>
                        </div>
                        <span class="text-primary">Xem chi tiết <i class="bi bi-chevron-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 48px;"></i>
                <p class="mt-2">Chưa có chuyến xe nào trong dự án này.</p>
                <a href="{{ route('trips.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm chuyến đầu tiên
                </a>
            </div>
        </div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        series: [{
            name: 'Số chuyến',
            type: 'column',
            data: @json($chartTripCount)
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        stroke: {
            width: [0, 4]
        },
        title: {
            text: ''
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [0, 1]
        },
        labels: @json($chartDates),
        xaxis: {
            type: 'category'
        },
        yaxis: {
            title: {
                text: 'Số chuyến',
            },
        },
        colors: ['#0d6efd', '#198754'],
        tooltip: {
            shared: true,
            intersect: false,
        }
    };

    var chart = new ApexCharts(document.querySelector("#dailyVolumeChart"), options);
    chart.render();

    window.addEventListener('theme-changed', function(e) {
        const isDark = e.detail.theme === 'dark';
        chart.updateOptions({
            theme: { mode: isDark ? 'dark' : 'light' }
        });
    });
});
</script>
<style>
    .hover-card { transition: transform 0.15s, box-shadow 0.15s; }
    .hover-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important; }
</style>
@endpush
@endsection
