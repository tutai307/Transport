@extends('layouts.app')
@section('title', 'Lương - ' . $employee->name)

@section('content')
{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Tính lương</a></li>
        <li class="breadcrumb-item active">{{ $employee->name }}</li>
    </ol>
</nav>

{{-- Thông tin tài xế --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 60px; height: 60px;">
                <i class="bi bi-person-fill fs-3"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="mb-0 fw-bold text-primary">{{ $employee->name }}</h4>
                @if($employee->phone)
                    <div class="text-muted"><i class="bi bi-telephone"></i> {{ $employee->phone }}</div>
                @endif
            </div>
            <div class="text-end d-none d-md-block">
                <div class="text-muted small">Tổng tất cả năm</div>
                <div class="fw-bold fs-5 text-success">{{ number_format($summary['total_salary'], 0, ',', '.') }}đ</div>
                <div class="text-muted small">{{ intval($summary['trip_count']) }} chuyến</div>
            </div>
        </div>
        {{-- Tổng tiền mobile --}}
        <div class="d-md-none mt-3 p-3 bg-light rounded text-center">
            <div class="text-muted small">Tổng tất cả năm</div>
            <div class="fw-bold fs-4 text-success">{{ number_format($summary['total_salary'], 0, ',', '.') }}đ</div>
            <div class="text-muted">{{ intval($summary['trip_count']) }} chuyến</div>
        </div>
    </div>
</div>

{{-- Danh sách năm --}}
<h5 class="mb-3 fw-semibold"><i class="bi bi-calendar3"></i> Chọn năm</h5>

@forelse($years as $yearData)
<a href="{{ route('payroll.by-year', [$employee, $yearData->year]) }}" class="text-decoration-none">
    <div class="card shadow-sm mb-3 hover-card border-start border-primary border-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="bg-primary text-white rounded-3 text-center px-3 py-2" style="min-width: 72px;">
                        <div class="fw-bold fs-4">{{ $yearData->year }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Số chuyến</div>
                            <div class="fw-bold fs-5 text-primary">{{ intval($yearData->trip_count) }}</div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Tổng lương</div>
                            <div class="fw-bold fs-5 text-success">{{ number_format($yearData->total_salary, 0, ',', '.') }}đ</div>
                        </div>
                    </div>
                </div>
                <div class="col-auto text-primary">
                    <i class="bi bi-chevron-right fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</a>
@empty
<div class="text-center text-muted py-5">
    <i class="bi bi-inbox" style="font-size: 48px;"></i>
    <p class="mt-2">Tài xế này chưa có chuyến xe nào.</p>
</div>
@endforelse

@push('styles')
<style>
    .hover-card { transition: transform 0.15s, box-shadow 0.15s; }
    .hover-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important; }
</style>
@endpush
@endsection
