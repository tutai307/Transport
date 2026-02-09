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
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-building"></i> {{ $project->name }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.export', ['project_id' => $project->id]) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel toàn bộ
            </a>
            <a href="{{ route('trips.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Thêm chuyến
            </a>
        </div>
    </div>
</div>

{{-- Tổng kết dự án --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center py-2">
                <small class="text-muted">Tổng số chuyến</small>
                <h4 class="text-primary mb-0">{{ number_format($projectSummary['total_trips']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body text-center py-2">
                <small class="text-muted">Tổng khối lượng</small>
                <h4 class="text-info mb-0">{{ number_format($projectSummary['total_volume'], 2) }} m³</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center py-2">
                <small class="text-muted">Tổng tiền</small>
                <h4 class="text-success mb-0">{{ number_format($projectSummary['total_price'], 0, ',', '.') }} đ</h4>
            </div>
        </div>
    </div>
</div>

{{-- Danh sách tháng --}}
<div class="row">
    @forelse($months as $m)
        <div class="col-md-6 col-lg-4 mb-3">
            <a href="{{ route('trips.by-month', ['project' => $project->id, 'year' => $m->year, 'month' => $m->month]) }}"
               class="text-decoration-none">
                <div class="card shadow-sm hover-card">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="bi bi-calendar-month"></i>
                            Tháng {{ $m->month }}/{{ $m->year }}
                        </h5>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-muted small">Chuyến</div>
                                <div class="fw-bold fs-5">{{ number_format($m->trip_count) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">KL (m³)</div>
                                <div class="fw-bold">{{ number_format($m->total_volume, 1) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Tiền</div>
                                <div class="fw-bold text-success">{{ number_format($m->total_price, 0, ',', '.') }}đ</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                        <a href="{{ route('reports.export', ['project_id' => $project->id, 'year' => $m->year, 'month' => $m->month]) }}" 
                           class="btn btn-sm btn-outline-success" title="Xuất Excel tháng này">
                            <i class="bi bi-file-earmark-excel"></i> Xuất
                        </a>
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

@push('styles')
<style>
    .hover-card { transition: transform 0.15s, box-shadow 0.15s; }
    .hover-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important; }
</style>
@endpush
@endsection
