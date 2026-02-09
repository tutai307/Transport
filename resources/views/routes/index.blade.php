@extends('layouts.app')
@section('title', 'Tuyến đường')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-signpost-2"></i> Danh sách tuyến đường</h4>
    <a href="{{ route('routes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm tuyến
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Điểm đi</th>
                <th>Điểm đến</th>
                <th>Khoảng cách (km)</th>
                <th>Số chuyến</th>
                <th>Trạng thái</th>
                <th style="width:80px">Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routes as $i => $route)
                <tr class="{{ !$route->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $route->from_location }}</td>
                    <td class="fw-bold">{{ $route->to_location }}</td>
                    <td>{{ $route->distance_km ? number_format($route->distance_km, 1) : '—' }}</td>
                    <td>{{ $route->trips_count }}</td>
                    <td>
                        @if($route->is_active)
                            <span class="badge bg-success">Đang dùng</span>
                        @else
                            <span class="badge bg-secondary">Ngưng</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('routes.edit', $route) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
