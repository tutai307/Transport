@extends('layouts.app')
@section('title', 'Tính lương tài xế')

@section('content')
<div class="page-header mb-4">
    <div class="row align-items-center g-2">
        <div class="col">
            <h4 class="mb-0 fw-bold"><i class="bi bi-wallet2"></i> Tính lương tài xế</h4>
        </div>
    </div>
</div>

<div class="row g-3">
    @forelse($employees as $employee)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('payroll.by-driver', $employee) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm hover-card {{ $employee->is_active ? 'border-primary' : 'border-secondary' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0 fw-bold text-break {{ $employee->is_active ? 'text-primary' : 'text-secondary' }}">
                                {{ $employee->name }}
                            </h5>
                            @if($employee->phone)
                                <small class="text-muted"><i class="bi bi-telephone"></i> {{ $employee->phone }}</small>
                            @endif
                            @if(!$employee->is_active)
                                <span class="badge bg-secondary ms-1">Nghỉ việc</span>
                            @endif
                        </div>
                    </div>

                    <div class="row text-center g-2">
                        <div class="col-6">
                            <div class="bg-light rounded p-2">
                                <div class="text-muted small">Số chuyến</div>
                                <div class="fw-bold fs-5 text-primary">{{ intval($employee->trips_sum_quantity ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2">
                                <div class="text-muted small">Tổng lương</div>
                                <div class="fw-bold text-success" style="font-size: 0.95rem;">
                                    {{ number_format($employee->trips_sum_total_price ?? 0, 0, ',', '.') }}đ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-end">
                    <span class="text-primary small">Xem chi tiết <i class="bi bi-chevron-right"></i></span>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-people" style="font-size: 48px;"></i>
            <p class="mt-2">Chưa có tài xế nào. <a href="{{ route('employees.create') }}">Thêm tài xế</a></p>
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
