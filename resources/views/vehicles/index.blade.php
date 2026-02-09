@extends('layouts.app')
@section('title', 'Xe')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-truck-front"></i> Danh sách xe</h4>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm xe
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Biển số</th>
                <th>Thể tích mặc định (m³)</th>
                <th>Số chuyến</th>
                <th>Trạng thái</th>
                <th style="width:80px">Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $i => $vehicle)
                <tr class="{{ !$vehicle->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $vehicle->plate_number }}</td>
                    <td>{{ number_format($vehicle->default_volume_m3, 2) }}</td>
                    <td>{{ $vehicle->trips_count }}</td>
                    <td>
                        @if($vehicle->is_active)
                            <span class="badge bg-success">Đang dùng</span>
                        @else
                            <span class="badge bg-secondary">Ngưng dùng</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
