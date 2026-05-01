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
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md">
            <h4 class="mb-0 text-break"><i class="bi bi-calendar-month"></i> {{ $project->name }} — {{ $monthLabel }}</h4>
        </div>
        <div class="col-12 col-md-auto">
            <div class="d-flex gap-2 w-100 w-md-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle flex-fill flex-md-grow-0" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['project_id' => $project->id, 'month' => $month, 'year' => $year, 'export_type' => 'freight']) }}">
                            <i class="bi bi-currency-dollar"></i> Xuất theo giá cước
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['project_id' => $project->id, 'month' => $month, 'year' => $year, 'export_type' => 'profit']) }}">
                            <i class="bi bi-graph-up"></i> Xuất theo lợi nhuận
                        </a></li>
                    </ul>
                </div>
                <a href="{{ route('trips.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                    <i class="bi bi-plus-circle"></i> Thêm chuyến
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tổng kết tháng --}}
<div class="row g-2 mb-4">
    <div class="col-6 col-md-6">
        <div class="card border-primary h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0">{{ intval($summary['total_trips']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="card border-success h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng tiền</small>
                <h5 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
</div>

{{-- Bảng chi tiết --}}
{{-- Bảng chi tiết (Desktop) --}}
<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-hover table-striped shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Ngày</th>
                <th>Xe</th>
                <th>Tài xế</th>
                <th>Vật liệu</th>
                <th>Tuyến đường</th>
                <th class="text-end">Số chuyến</th>
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
                    <td class="text-end">{{ intval($trip->quantity) }}</td>
                    <td class="text-end fw-bold">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
                    <td>{{ Str::limit($trip->note, 30) }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('trips.destroy', $trip) }}" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xoá chuyến xe này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">Chưa có chuyến xe nào.</td>
                </tr>
            @endforelse
        </tbody>
        @if($trips->isNotEmpty())
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="6" class="text-end">Tổng cộng</td>
                <td class="text-end">{{ intval($summary['total_trips']) }}</td>
                <td class="text-end text-success">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Responsive Cards (Mobile) --}}
<div class="d-block d-md-none">
    @forelse($trips as $trip)
        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-primary">{{ $trip->trip_date->format('d/m/Y') }}</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('trips.edit', $trip) }}"><i class="bi bi-pencil text-primary"></i> Sửa</a></li>
                            <li>
                                <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Xoá</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Xe & Vật liệu</small>
                        <span class="fw-semibold">{{ $trip->vehicle->plate_number }}</span> — {{ $trip->material->name }}
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">Số chuyến</small>
                        <span class="fw-bold fs-5">{{ intval($trip->quantity) }}</span>
                    </div>
                    <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="text-success fw-bold fs-5">{{ number_format($trip->total_price, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5 card">
            <div class="card-body">Chưa có chuyến xe nào.</div>
        </div>
    @endforelse
</div>
@endsection
