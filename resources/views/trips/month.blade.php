@extends('layouts.app')
@section('title', $project->name . ' - ' . $monthLabel)

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Chuyến xe</a></li>
            <li class="breadcrumb-item"><a href="{{ route('trips.by-project', $project) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active">{{ $monthLabel }}</li>
        </ol>
    </nav>
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md">
            <h4 class="mb-0 text-break"><i class="bi bi-calendar-month"></i> {{ $project->name }} — {{ $monthLabel }}</h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="{{ route('reports.export', ['project_id' => $project->id, 'month' => $month, 'year' => $year]) }}"
                   class="btn btn-success btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                </a>
                <a href="{{ route('trips.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-plus-circle"></i> Thêm chuyến
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tổng kết tháng --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-primary h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng số chuyến</small>
                <h5 class="text-primary mb-0">{{ intval($summary['total_trips']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-success h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng cước</small>
                <h5 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-warning h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Phát sinh lương (số bản ghi)</small>
                <h5 class="text-warning mb-0">{{ intval($summary['total_adj_count']) }}</h5>
            </div>
        </div>
    </div>
</div>

{{-- Lịch tháng: 7 cột (T2..CN) --}}
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-none d-md-grid gap-2 mb-2" style="grid-template-columns: repeat(7, 1fr);">
            @foreach(['T2','T3','T4','T5','T6','T7','CN'] as $wd)
                <div class="text-center small text-muted fw-semibold">{{ $wd }}</div>
            @endforeach
        </div>

        <div class="d-grid gap-2" style="grid-template-columns: repeat(7, 1fr);">
            {{-- Ô trống cho các ngày trước ngày 1 --}}
            @for($i = 0; $i < $leadingBlanks; $i++)
                <div></div>
            @endfor

            @foreach($days as $d)
                @php
                    $hasTrips = $d['trip_count'] > 0;
                    $hasAdj = $d['adj_count'] > 0;
                    $isClickable = $hasTrips || $hasAdj;
                @endphp
                @if($isClickable)
                    <a href="{{ route('trips.by-day', ['project' => $project->id, 'year' => $year, 'month' => $month, 'day' => $d['day']]) }}"
                       class="text-decoration-none">
                        <div class="card border-primary h-100 day-card day-card-active">
                            <div class="card-body p-2 text-center">
                                <div class="fw-bold text-primary day-num">{{ $d['day'] }}</div>
                                @if($hasTrips)
                                    <div class="day-detail small text-dark mt-1">
                                        <i class="bi bi-truck"></i> {{ intval($d['trip_count']) }} chuyến
                                    </div>
                                    <div class="day-detail small text-success fw-semibold">
                                        {{ number_format($d['total_price'], 0, ',', '.') }}đ
                                    </div>
                                    <div class="day-dot-wrap">
                                        <span class="day-dot bg-primary"></span>
                                        @if($hasAdj)<span class="day-dot bg-warning"></span>@endif
                                    </div>
                                @endif
                                @if($hasAdj && !$hasTrips)
                                    <div class="day-detail small text-warning">
                                        <i class="bi bi-cash-coin"></i> {{ intval($d['adj_count']) }} PS
                                    </div>
                                    <div class="day-dot-wrap">
                                        <span class="day-dot bg-warning"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @else
                    <div class="card border-light h-100 day-card day-card-empty">
                        <div class="card-body p-2 text-center">
                            <div class="text-muted day-num">{{ $d['day'] }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Danh sách ngày có dữ liệu (mobile only) --}}
<div class="d-md-none mt-3">
    @php $hasDays = collect($days)->where('trip_count', '>', 0)->merge(collect($days)->where('adj_count', '>', 0))->unique('day')->sortBy('day'); @endphp
    @if($hasDays->isNotEmpty())
    <div class="list-group shadow-sm">
        @foreach($hasDays as $d)
            <a href="{{ route('trips.by-day', ['project' => $project->id, 'year' => $year, 'month' => $month, 'day' => $d['day']]) }}"
               class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bold text-primary" style="font-size:1.2rem; min-width:28px; text-align:center;">{{ $d['day'] }}</div>
                    <div>
                        @if($d['trip_count'] > 0)
                            <div class="small fw-semibold"><i class="bi bi-truck me-1 text-primary"></i>{{ intval($d['trip_count']) }} chuyến</div>
                        @endif
                        @if($d['adj_count'] > 0)
                            <div class="small text-warning"><i class="bi bi-cash-coin me-1"></i>{{ intval($d['adj_count']) }} phát sinh</div>
                        @endif
                    </div>
                </div>
                @if($d['trip_count'] > 0)
                <div class="text-end">
                    <div class="fw-semibold text-success small">{{ number_format($d['total_price'], 0, ',', '.') }}đ</div>
                </div>
                @endif
            </a>
        @endforeach
    </div>
    @endif
</div>

@push('styles')
<style>
    .day-card { min-height: 90px; transition: transform 0.15s; }
    .day-card-active:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(13,110,253,.15); }
    .day-card-empty { background: transparent; }
    .day-num { font-size: 18px; }
    .day-dot-wrap { display: none; gap: 3px; justify-content: center; margin-top: 3px; }
    .day-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; }

    @media (max-width: 767px) {
        .d-grid[style*="repeat(7"] { gap: 3px !important; }
        .day-card { min-height: 52px; }
        .day-card .card-body { padding: 5px 2px !important; }
        .day-num { font-size: 14px !important; }
        /* Ẩn text chi tiết, hiện dot indicator */
        .day-detail { display: none !important; }
        .day-dot-wrap { display: flex !important; }
    }
</style>
@endpush
@endsection
