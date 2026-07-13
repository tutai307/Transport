@extends('layouts.app')
@section('title', $vehicle->plate_number . ' — ' . $monthLabel)

@section('content')
{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('vehicle-stats.index') }}">Thống kê theo xe</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vehicle-stats.by-vehicle', $vehicle) }}">{{ $vehicle->plate_number }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vehicle-stats.by-year', [$vehicle, $year]) }}">Năm {{ $year }}</a></li>
        <li class="breadcrumb-item active">{{ $monthLabel }}</li>
    </ol>
</nav>

<div class="page-header mb-3">
    <h4 class="mb-0 fw-bold">
        <i class="bi bi-calendar-month"></i> {{ $vehicle->plate_number }}
        <span class="text-muted fw-normal fs-5">— {{ $monthLabel }}</span>
    </h4>
</div>

{{-- Tổng kết tháng --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-primary h-100 shadow-sm">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0 fw-bold">{{ intval($summary['trip_count']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-9">
        <div class="card bg-success text-white h-100 shadow-sm border-success">
            <div class="card-body text-center p-2">
                <small class="text-white-50 d-block small">Doanh thu tháng</small>
                <h4 class="mb-0 fw-bold">{{ number_format($summary['total_amount'], 0, ',', '.') }}đ</h4>
            </div>
        </div>
    </div>
</div>

{{-- Bảng chi tiết (Desktop) --}}
<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-hover table-striped shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Ngày</th>
                <th>Tài xế</th>
                <th>Dự án</th>
                <th>Vật liệu</th>
                <th>Tuyến đường</th>
                <th class="text-end">Số lượng</th>
                <th class="text-end">Giá cước</th>
                <th class="text-end">Thành tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trips as $i => $trip)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                <td>{{ $trip->driver->name ?? $trip->driver_name_snapshot ?? '—' }}</td>
                <td>{{ $trip->project->name }}</td>
                <td>{{ $trip->material->name }}</td>
                <td>{{ $trip->route->full_name }}</td>
                <td class="text-end">{{ intval($trip->quantity) }}</td>
                <td class="text-end">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</td>
                <td class="text-end fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
                <td>{{ Str::limit($trip->note, 30) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">Không có chuyến xe nào.</td>
            </tr>
            @endforelse
        </tbody>
        @if($trips->isNotEmpty())
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="6" class="text-end">Tổng cộng</td>
                <td class="text-end">{{ intval($trips->sum(fn($t) => $t->quantity)) }}</td>
                <td></td>
                <td class="text-end text-success">{{ number_format($summary['total_amount'], 0, ',', '.') }}đ</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Cards (Mobile) --}}
<div class="d-block d-md-none">
    @forelse($trips as $trip)
    <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-primary">{{ $trip->trip_date->format('d/m/Y') }}</span>
                <span class="fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</span>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted d-block">Tài xế</small>
                    <span class="fw-semibold">{{ $trip->driver->name ?? $trip->driver_name_snapshot ?? '—' }}</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Dự án</small>
                    <span>{{ Str::limit($trip->project->name, 20) }}</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Vật liệu</small>
                    <span>{{ $trip->material->name }}</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Tuyến đường</small>
                    <span>{{ Str::limit($trip->route->full_name, 25) }}</span>
                </div>
                <div class="col-6 pt-2 border-top">
                    <small class="text-muted d-block">Số lượng</small>
                    <span class="fw-bold">{{ $trip->quantity + 0 }}</span>
                </div>
                <div class="col-6 pt-2 border-top">
                    <small class="text-muted d-block">Giá cước</small>
                    <span class="fw-bold">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</span>
                </div>
                @if($trip->note)
                <div class="col-12">
                    <small class="text-muted d-block">Ghi chú</small>
                    <span class="fst-italic">{{ $trip->note }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center text-muted py-5 card">
        <div class="card-body">Không có chuyến xe nào.</div>
    </div>
    @endforelse
</div>
@endsection
