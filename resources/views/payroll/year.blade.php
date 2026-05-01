@extends('layouts.app')
@section('title', 'Lương ' . $year . ' - ' . $employee->name)

@section('content')
@php
    $monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5',
                   'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
@endphp

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Tính lương</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payroll.by-driver', $employee) }}">{{ $employee->name }}</a></li>
        <li class="breadcrumb-item active">Năm {{ $year }}</li>
    </ol>
</nav>

{{-- Header --}}
<div class="page-header mb-4">
    <div class="row align-items-center g-2">
        <div class="col">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-wallet2"></i> {{ $employee->name }}
                <span class="text-muted fw-normal fs-5">— Năm {{ $year }}</span>
            </h4>
        </div>
    </div>
</div>

{{-- Tổng quan năm --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Tháng có chuyến</div>
                <div class="fw-bold fs-4 text-primary">{{ $months->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Tổng số chuyến</div>
                <div class="fw-bold fs-4 text-primary">{{ $yearTotal['trip_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(45deg, #198754, #20c997);">
            <div class="card-body py-3">
                <div class="text-white text-opacity-75 small mb-1">Tổng lương cả năm</div>
                <div class="fw-bold fs-3 text-white">{{ number_format($yearTotal['total_salary'], 0, ',', '.') }}đ</div>
            </div>
        </div>
    </div>
</div>

{{-- Bảng lương theo tháng --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-table"></i> Lương theo tháng</h6>
    </div>
    <div class="card-body p-0">
        @forelse($months as $row)
        <a href="{{ route('payroll.by-month', [$employee, $year, $row->month]) }}" class="text-decoration-none text-reset">
            <div class="row align-items-center g-0 border-bottom px-3 py-3 hover-row">
                <div class="col-auto me-3">
                    <div class="bg-primary-subtle text-primary rounded-3 text-center px-3 py-2" style="min-width: 90px;">
                        <div class="fw-bold fs-6">{{ $monthNames[$row->month] }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="row g-2">
                        <div class="col-5">
                            <div class="text-muted small">Số chuyến</div>
                            <div class="fw-bold text-primary">{{ $row->trip_count }} chuyến</div>
                        </div>
                        <div class="col-5">
                            <div class="text-muted small">Tiền lương</div>
                            <div class="fw-bold text-success">{{ number_format($row->total_salary, 0, ',', '.') }}đ</div>
                        </div>
                        <div class="col-2 text-end text-primary">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size: 48px;"></i>
            <p class="mt-2">Không có dữ liệu.</p>
        </div>
        @endforelse

        @if($months->isNotEmpty())
        <div class="row align-items-center g-0 px-3 py-3 bg-light fw-bold">
            <div class="col-auto me-3">
                <div class="rounded-3 text-center px-3 py-2" style="min-width: 90px;">
                    <div class="text-muted fs-6">Tổng cộng</div>
                </div>
            </div>
            <div class="col">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="text-muted small">Số chuyến</div>
                        <div class="fw-bold text-primary fs-5">{{ $yearTotal['trip_count'] }} chuyến</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Tiền lương</div>
                        <div class="fw-bold text-success fs-5">{{ number_format($yearTotal['total_salary'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .hover-row { transition: background 0.12s; cursor: pointer; }
    .hover-row:hover { background: rgba(13, 110, 253, 0.06); }
    [data-bs-theme="dark"] .hover-row:hover { background: rgba(13, 110, 253, 0.12); }
</style>
@endpush
@endsection
