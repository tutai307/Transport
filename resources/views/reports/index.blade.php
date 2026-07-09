@extends('layouts.app')
@section('title', 'Xuất hoá đơn')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-spreadsheet"></i> Xuất hoá đơn</h4>
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
    <div class="col-md-4">
        <label class="form-label">Tài xế</label>
        <select name="driver_id" class="form-select select2" data-placeholder="-- Tất cả tài xế --">
            <option value="">-- Tất cả tài xế --</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('driver_id') == $emp->id ? 'selected' : '' }}>
                    {{ $emp->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Lọc
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
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng số chuyến</h6>
                <h3 class="text-primary mb-0">{{ intval($summary['total_trips']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted">Tổng tiền cước</h6>
                <h3 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</h3>
            </div>
        </div>
    </div>
</div>

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
                <th class="text-end">Số chuyến</th>
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
                    <td>{{ $trip->driver_name_snapshot ?? optional($trip->driver)->name ?? '—' }}</td>
                    <td>{{ $trip->material->name }}</td>
                    <td>{{ $trip->route->full_name }}</td>
                    <td class="text-end">{{ intval($trip->quantity) }}</td>
                    <td class="text-end">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</td>
                    <td class="text-end fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
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
                    <td class="text-end">{{ intval($summary['total_trips']) }}</td>
                    <td></td>
                    <td class="text-end text-success">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection
