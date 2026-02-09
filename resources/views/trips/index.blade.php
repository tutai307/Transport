@extends('layouts.app')
@section('title', 'Chuyến xe theo dự án')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-card-list"></i> Chuyến xe theo dự án</h4>
    <a href="{{ route('trips.create') }}" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle"></i> Thêm chuyến
    </a>
</div>

<div class="row">
    @forelse($projects as $project)
        @if($project->trips_count > 0 || $project->is_active)
        <div class="col-md-6 col-lg-4 mb-3">
            <a href="{{ route('trips.by-project', $project) }}" class="text-decoration-none">
                <div class="card h-100 {{ $project->is_active ? 'border-primary' : 'border-secondary' }} shadow-sm hover-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 {{ $project->is_active ? 'text-primary' : 'text-secondary' }}">
                                <i class="bi bi-building"></i> {{ $project->name }}
                            </h5>
                            @if(!$project->is_active)
                                <span class="badge bg-secondary">Tạm ngưng</span>
                            @endif
                        </div>

                        @if($project->description)
                            <p class="card-text text-muted small mb-2">{{ Str::limit($project->description, 60) }}</p>
                        @endif

                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <div class="text-muted small">Số chuyến</div>
                                <div class="fw-bold fs-5">{{ number_format($project->trips_count) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Khối lượng</div>
                                <div class="fw-bold fs-6">{{ number_format($project->trips_sum_volume_m3 ?? 0, 1) }} m³</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Tổng tiền</div>
                                <div class="fw-bold fs-6 text-success">{{ number_format($project->trips_sum_total_price ?? 0, 0, ',', '.') }}đ</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent text-end">
                        <span class="text-primary">Xem chi tiết <i class="bi bi-chevron-right"></i></span>
                    </div>
                </div>
            </a>
        </div>
        @endif
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 48px;"></i>
                <p class="mt-2">Chưa có dự án nào. <a href="{{ route('projects.create') }}">Tạo dự án đầu tiên</a></p>
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
