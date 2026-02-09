@extends('layouts.app')
@section('title', 'Báo cáo')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-spreadsheet"></i> Báo cáo chuyến xe</h4>
</div>

{{-- Bộ lọc --}}
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-2">
        <label class="form-label">Từ ngày</label>
        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Đến ngày</label>
        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Dự án</label>
        <select name="project_id" class="form-select">
            <option value="">-- Tất cả --</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Xe</label>
        <select name="vehicle_id" class="form-select">
            <option value="">-- Tất cả --</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                    {{ $vehicle->plate_number }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i>
        </button>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success w-100">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </a>
    </div>
</form>

{{-- Tổng kết --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng số chuyến</h6>
                <h3 class="text-primary mb-0">{{ number_format($summary['total_trips']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng khối lượng</h6>
                <h3 class="text-info mb-0">{{ number_format($summary['total_volume'], 2) }} m³</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng tiền</h6>
                <h3 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng lợi nhuận</h6>
                <h3 class="text-warning mb-0">{{ number_format($summary['total_profit'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

@if(isset($profitByProject) && count($profitByProject) > 0)
    <h5 class="mb-3 text-secondary"><i class="bi bi-graph-up"></i> Chi tiết lợi nhuận theo dự án</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Dự án</th>
                    <th class="text-center">Số chuyến</th>
                    <th class="text-end">Lợi nhuận</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profitByProject as $item)
                    <tr>
                        <td>{{ $item['project_name'] }}</td>
                        <td class="text-center">{{ $item['trip_count'] }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($item['total_profit'], 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Bảng chi tiết --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped table-sm">
        <thead class="table-primary">
            <tr>
                <th>STT</th>
                <th>Ngày</th>
                <th>Dự án</th>
                <th>Xe</th>
                <th>Tài xế</th>
                <th>Vật liệu</th>
                <th>Tuyến đường</th>
                <th class="text-end">KL (m³)</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trips as $i => $trip)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                    <td>{{ $trip->project->name }}</td>
                    <td>{{ $trip->vehicle->plate_number }}</td>
                    <td>{{ $trip->driver->name }}</td>
                    <td>{{ $trip->material->name }}</td>
                    <td>{{ $trip->route->full_name }}</td>
                    <td class="text-end">{{ number_format($trip->volume_m3, 2) }}</td>
                    <td class="text-end">{{ number_format($trip->price_per_m3, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($trip->total_price, 0, ',', '.') }}</td>
                    <td>{{ Str::limit($trip->note, 30) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-3">Không có dữ liệu trong khoảng thời gian này.</td>
                </tr>
            @endforelse
        </tbody>
        @if($trips->count() > 0)
            <tfoot class="table-warning">
                <tr class="fw-bold">
                    <td colspan="7" class="text-end">TỔNG CỘNG:</td>
                    <td class="text-end">{{ number_format($summary['total_volume'], 2) }}</td>
                    <td></td>
                    <td class="text-end">{{ number_format($summary['total_price'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection
