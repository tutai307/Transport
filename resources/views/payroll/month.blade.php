@extends('layouts.app')
@section('title', $employee->name . ' — ' . $monthLabel)

@section('content')
{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Tính lương</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payroll.by-driver', $employee) }}">{{ $employee->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payroll.by-year', [$employee, $year]) }}">Năm {{ $year }}</a></li>
        <li class="breadcrumb-item active">{{ $monthLabel }}</li>
    </ol>
</nav>

@php
    $defaultFrom = \Carbon\Carbon::create($year, $month, 1)->format('Y-m-d');
    $defaultTo   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
@endphp

<div class="page-header mb-3">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-calendar-month"></i> {{ $employee->name }}
                <span class="text-muted fw-normal fs-5">— {{ $monthLabel }}</span>
            </h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="bg-light p-2 rounded border d-flex align-items-center gap-2 flex-wrap">
                <small class="text-muted mb-0 fw-bold"><i class="bi bi-file-earmark-excel text-success"></i> Phiếu lương:</small>
                <form action="{{ route('payroll.export', $employee) }}" method="GET" class="d-flex align-items-center gap-2 mb-0">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ $defaultFrom }}" style="width: auto; min-width: 130px; min-height: 31px; height: 31px;">
                    <span class="text-muted">→</span>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ $defaultTo }}" style="width: auto; min-width: 130px; min-height: 31px; height: 31px;">
                    <button type="submit" class="btn btn-success btn-sm py-1 px-3" style="min-height: 31px;">
                        <i class="bi bi-download"></i> Xuất Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tổng kết tháng --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-2">
        <div class="card border-primary h-100 shadow-sm">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0 fw-bold">{{ intval($summary['trip_count']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-secondary h-100 shadow-sm">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Lương chuyến</small>
                <h5 class="text-dark mb-0 fw-bold">{{ number_format($summary['total_salary'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-info h-100 shadow-sm">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Phụ cấp (+)</small>
                <h5 class="text-info mb-0 fw-bold">{{ number_format($summary['total_additions'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-danger h-100 shadow-sm">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tạm ứng (-)</small>
                <h5 class="text-danger mb-0 fw-bold">{{ number_format($summary['total_deductions'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card bg-success text-white h-100 shadow-sm border-success">
            <div class="card-body text-center p-2">
                <small class="text-white-50 d-block small">Thực lĩnh</small>
                <h4 class="mb-0 fw-bold">{{ number_format($summary['net_salary'], 0, ',', '.') }}đ</h4>
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
                <th>Xe</th>
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
                <td>{{ $trip->vehicle->plate_number }}</td>
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
                <td class="text-end text-success">{{ number_format($summary['total_salary'], 0, ',', '.') }}đ</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Bảng chi tiết phát sinh --}}
@if($adjustments->isNotEmpty())
<h5 class="mt-4 mb-2 text-primary fw-bold"><i class="bi bi-cash-stack"></i> Các khoản phụ cấp / tạm ứng trong tháng</h5>
<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-hover table-striped shadow-sm">
        <thead class="table-warning">
            <tr>
                <th>#</th>
                <th>Ngày</th>
                <th>Dự án</th>
                <th>Loại phát sinh</th>
                <th class="text-end">Số tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adjustments as $i => $adj)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $adj->trip_date->format('d/m/Y') }}</td>
                <td>{{ $adj->project->name }}</td>
                <td>
                    @if($adj->type === 'addition')
                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-plus-circle"></i> Phụ cấp / Chi hộ</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-dash-circle"></i> Tạm ứng / Trừ</span>
                    @endif
                </td>
                <td class="text-end fw-bold {{ $adj->type === 'addition' ? 'text-success' : 'text-danger' }}">
                    {{ $adj->type === 'addition' ? '+' : '-' }}{{ number_format($adj->amount, 0, ',', '.') }}đ
                </td>
                <td>{{ $adj->note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

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
                    <small class="text-muted d-block">Xe</small>
                    <span class="fw-semibold">{{ $trip->vehicle->plate_number }}</span>
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

{{-- Cards Phát sinh (Mobile) --}}
@if($adjustments->isNotEmpty())
<h5 class="mt-4 mb-2 text-primary fw-bold d-block d-md-none"><i class="bi bi-cash-stack"></i> Khoản phát sinh</h5>
<div class="d-block d-md-none">
    @foreach($adjustments as $adj)
    <div class="card mb-3 shadow-sm border-0 border-start border-4 {{ $adj->type === 'addition' ? 'border-success' : 'border-danger' }}">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-primary">{{ $adj->trip_date->format('d/m/Y') }}</span>
                @if($adj->type === 'addition')
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Phụ cấp</span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tạm ứng</span>
                @endif
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted d-block">Dự án</small>
                    <span class="fw-semibold">{{ Str::limit($adj->project->name, 25) }}</span>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Số tiền</small>
                    <span class="fw-bold {{ $adj->type === 'addition' ? 'text-success' : 'text-danger' }}">
                        {{ $adj->type === 'addition' ? '+' : '-' }}{{ number_format($adj->amount, 0, ',', '.') }}đ
                    </span>
                </div>
                @if($adj->note)
                <div class="col-12 pt-2 border-top">
                    <small class="text-muted"><i class="bi bi-info-circle"></i> {{ $adj->note }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
