@extends('layouts.app')
@section('title', $project->name . ' - ' . $dayLabel)

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Chuyến xe</a></li>
            <li class="breadcrumb-item"><a href="{{ route('trips.by-project', $project) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('trips.by-month', ['project' => $project->id, 'year' => $year, 'month' => $month]) }}">Tháng {{ $month }}/{{ $year }}</a></li>
            <li class="breadcrumb-item active">Ngày {{ $dayLabel }}</li>
        </ol>
    </nav>
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md">
            <h4 class="mb-0 text-break"><i class="bi bi-calendar-day"></i> {{ $project->name }} — Ngày {{ $dayLabel }}</h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="{{ route('trips.create', ['project_id' => $project->id, 'trip_date' => sprintf('%04d-%02d-%02d', $year, $month, $day)]) }}"
                   class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-plus-circle"></i> Thêm chuyến ngày này
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tổng kết ngày --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0">{{ intval($summary['total_trips']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-success h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng cước</small>
                <h5 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-info h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Cộng thêm</small>
                <h5 class="text-info mb-0">{{ number_format($summary['total_additions'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-danger h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Trừ bớt</small>
                <h5 class="text-danger mb-0">{{ number_format($summary['total_deductions'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
</div>

{{-- Bảng chi tiết chuyến xe --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-truck"></i> Danh sách chuyến xe
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Xe</th>
                    <th>Tài xế</th>
                    <th>Vật liệu</th>
                    <th>Tuyến đường</th>
                    <th class="text-end">Số chuyến</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                    <th>Ghi chú</th>
                    <th style="width:80px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $i => $trip)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $trip->vehicle_plate_snapshot ?? optional($trip->vehicle)->plate_number ?? '—' }}</td>
                        <td>{{ $trip->driver_name_snapshot ?? optional($trip->driver)->name ?? '—' }}</td>
                        <td>{{ optional($trip->material)->name ?? '—' }}</td>
                        <td class="small">{{ optional($trip->route)->from_location }} → {{ optional($trip->route)->to_location }}</td>
                        <td class="text-end">{{ intval($trip->quantity) }}</td>
                        <td class="text-end">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</td>
                        <td class="text-end fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
                        <td class="small text-muted">{{ Str::limit($trip->note, 25) }}</td>
                        <td>
                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Không có chuyến xe nào trong ngày này.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Phát sinh lương trong ngày --}}
@if($adjustments->count())
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-warning text-dark">
        <i class="bi bi-cash-coin"></i> Phát sinh lương trong ngày
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tài xế</th>
                    <th>Loại</th>
                    <th class="text-end">Số tiền</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach($adjustments as $adj)
                    <tr>
                        <td>{{ optional($adj->driver)->name ?? '—' }}</td>
                        <td>
                            @if($adj->type === 'addition')
                                <span class="badge bg-success">Cộng thêm</span>
                            @else
                                <span class="badge bg-danger">Trừ bớt</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $adj->type === 'addition' ? 'text-success' : 'text-danger' }}">
                            {{ $adj->type === 'addition' ? '+' : '-' }}{{ number_format($adj->amount, 0, ',', '.') }}đ
                        </td>
                        <td class="small text-muted">{{ $adj->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Thống kê theo Xe (dựa vào snapshot biển số + tài xế) --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-info text-white">
        <i class="bi bi-truck-front"></i> Thống kê theo xe
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Biển số xe</th>
                    <th>Tài xế</th>
                    <th class="text-end">Số chuyến</th>
                    <th class="text-end">Tổng cước</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byVehicle as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['plate'] }}</td>
                        <td>{{ $row['driver_name'] }}</td>
                        <td class="text-end">{{ intval($row['trip_count']) }}</td>
                        <td class="text-end text-success fw-bold">{{ number_format($row['total_price'], 0, ',', '.') }}đ</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Thống kê theo Vật liệu --}}
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-secondary text-white">
        <i class="bi bi-box-seam"></i> Thống kê theo vật liệu
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vật liệu</th>
                    <th class="text-end">Số chuyến</th>
                    <th class="text-end">Tổng cước</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byMaterial as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['material'] }}</td>
                        <td class="text-end">{{ intval($row['trip_count']) }}</td>
                        <td class="text-end text-success fw-bold">{{ number_format($row['total_price'], 0, ',', '.') }}đ</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Thống kê theo Cung chặng --}}
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-signpost-split"></i> Thống kê theo cung chặng
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cung chặng</th>
                    <th class="text-end">Số chuyến</th>
                    <th class="text-end">Tổng cước</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byRoute as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['route'] }}</td>
                        <td class="text-end">{{ intval($row['trip_count']) }}</td>
                        <td class="text-end text-success fw-bold">{{ number_format($row['total_price'], 0, ',', '.') }}đ</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
