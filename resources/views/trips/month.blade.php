@extends('layouts.app')
@section('title', $project->name . ' - ' . $monthLabel)

@section('content')
<div class="page-header">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Chuyến xe</a></li>
            <li class="breadcrumb-item"><a href="{{ route('trips.by-project', $project) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active">{{ $monthLabel }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="bi bi-calendar-month"></i> {{ $project->name }} — {{ $monthLabel }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.export', ['project_id' => $project->id, 'month' => $month, 'year' => $year]) }}"
               class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </a>
            <a href="{{ route('trips.create', ['project_id' => $project->id, 'trip_date' => sprintf('%d-%02d-01', $year, $month)]) }}"
               class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Thêm chuyến
            </a>
        </div>
    </div>
</div>

{{-- Tổng kết tháng --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-2 text-center">
                <small class="text-muted">Số chuyến</small>
                <h5 class="mb-0">{{ number_format($summary['total_trips']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-2 text-center">
                <small class="text-muted">Tổng khối lượng</small>
                <h5 class="mb-0">{{ number_format($summary['total_volume'], 2) }} m³</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-2 text-center">
                <small class="text-muted">Tổng tiền</small>
                <h5 class="mb-0 text-success">{{ number_format($summary['total_price'], 0, ',', '.') }} đ</h5>
            </div>
        </div>
    </div>
</div>

{{-- Bảng chi tiết --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Ngày</th>
                <th>Xe</th>
                <th>Tài xế</th>
                <th>Vật liệu</th>
                <th>Tuyến đường</th>
                <th class="text-end">KL (m³)</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
                <th>Ghi chú</th>
                <th style="width:100px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trips as $i => $trip)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                    <td>{{ $trip->vehicle->plate_number }}</td>
                    <td>{{ $trip->driver->name }}</td>
                    <td>{{ $trip->material->name }}</td>
                    <td>{{ $trip->route->full_name }}</td>
                    <td class="text-end">{{ number_format($trip->volume_m3, 2) }}</td>
                    <td class="text-end">{{ number_format($trip->price_per_m3, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($trip->total_price, 0, ',', '.') }}</td>
                    <td>{{ Str::limit($trip->note, 30) }}</td>
                    <td>
                        <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('trips.destroy', $trip) }}" class="d-inline"
                              onsubmit="return confirm('Bạn có chắc muốn xoá chuyến xe này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        Chưa có chuyến xe nào trong tháng này.
                        <a href="{{ route('trips.create', ['project_id' => $project->id, 'trip_date' => sprintf('%d-%02d-01', $year, $month)]) }}">
                            Thêm chuyến đầu tiên
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($trips->count() > 0)
            <tfoot class="table-warning">
                <tr class="fw-bold">
                    <td colspan="6" class="text-end">TỔNG CỘNG:</td>
                    <td class="text-end">{{ number_format($summary['total_volume'], 2) }}</td>
                    <td></td>
                    <td class="text-end">{{ number_format($summary['total_price'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection
