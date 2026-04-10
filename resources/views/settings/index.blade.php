@extends('layouts.app')
@section('title', 'Cài đặt danh mục')

@section('content')
<div class="page-header mb-4">
    <h4 class="mb-0"><i class="bi bi-gear"></i> Cài đặt & Danh mục</h4>
</div>

<div class="row g-3">
    @foreach($modules as $module)
    <div class="col-12 col-md-6">
        <a href="{{ route($module['route']) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-start border-4 border-{{ $module['color'] }} hover-card transition-all">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle bg-{{ $module['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center me-4 flex-shrink-0" style="width: 64px; height: 64px;">
                        <i class="bi {{ $module['icon'] }} fs-2 text-{{ $module['color'] }}"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 text-dark fw-bold">{{ $module['title'] }}</h5>
                        <p class="card-text text-muted small mb-0">{{ $module['description'] }}</p>
                    </div>
                    <div class="ms-auto text-muted">
                        <i class="bi bi-chevron-right fs-4"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@push('styles')
<style>
    .hover-card:active {
        transform: scale(0.98);
        background-color: #f8f9fa;
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush
@endsection
