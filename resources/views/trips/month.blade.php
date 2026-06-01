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
    <div class="col-6 col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Số chuyến</small>
                <h5 class="text-primary mb-0">{{ intval($summary['total_trips']) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-success h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tổng tiền vận chuyển</small>
                <h5 class="text-success mb-0">{{ number_format($summary['total_price'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-info h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Phụ cấp phát sinh (+)</small>
                <h5 class="text-info mb-0">{{ number_format($summary['total_additions'], 0, ',', '.') }}đ</h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-danger h-100">
            <div class="card-body text-center p-2">
                <small class="text-muted d-block small">Tạm ứng / Khấu trừ (-)</small>
                <h5 class="text-danger mb-0">{{ number_format($summary['total_deductions'], 0, ',', '.') }}đ</h5>
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
            @forelse($allRecords as $i => $item)
                @if(isset($item->is_adjustment) && $item->is_adjustment)
                    <tr class="table-warning-subtle">
                        <td>{{ $i + 1 }}</td>
                        <td class="text-nowrap">{{ $item->trip_date->format('d/m/Y') }}</td>
                        <td class="text-muted text-center">-</td>
                        <td>{{ $item->driver->name }}</td>
                        <td>
                            @if($item->type === 'addition')
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-plus-circle"></i> Phụ cấp / Chi hộ</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-dash-circle"></i> Tạm ứng / Trừ</span>
                            @endif
                        </td>
                        <td class="text-muted text-center">-</td>
                        <td class="text-muted text-center">-</td>
                        <td class="text-end fw-bold {{ $item->type === 'addition' ? 'text-success' : 'text-danger' }}">
                            {{ $item->type === 'addition' ? '+' : '-' }}{{ number_format($item->amount, 0, ',', '.') }}đ
                        </td>
                        <td>{{ Str::limit($item->note, 30) }}</td>
                        <td>
                            <form method="POST" action="{{ route('salary-adjustments.destroy', $item) }}" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xoá khoản phát sinh này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="text-nowrap">{{ $item->trip_date->format('d/m/Y') }}</td>
                        <td>{{ $item->vehicle->plate_number }}</td>
                        <td>{{ $item->driver->name }}</td>
                        <td>{{ $item->material->name }}</td>
                        <td>{{ $item->route->full_name }}</td>
                        <td class="text-end">{{ intval($item->quantity) }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                        <td>{{ Str::limit($item->note, 30) }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('trips.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('trips.destroy', $item) }}" class="d-inline"
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
                @endif
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">Chưa có chuyến xe hay khoản phát sinh nào.</td>
                </tr>
            @endforelse
        </tbody>
        @if($allRecords->isNotEmpty())
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="6" class="text-end">Tổng cộng vận chuyển</td>
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
    @forelse($allRecords as $item)
        @if(isset($item->is_adjustment) && $item->is_adjustment)
            <div class="card mb-3 shadow-sm border-0 border-start border-4 {{ $item->type === 'addition' ? 'border-success' : 'border-danger' }}">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-primary">{{ $item->trip_date->format('d/m/Y') }}</span>
                        <div class="d-flex align-items-center gap-2">
                            @if($item->type === 'addition')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Phụ cấp</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tạm ứng</span>
                            @endif
                            <form method="POST" action="{{ route('salary-adjustments.destroy', $item) }}" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Tài xế</small>
                            <span class="fw-semibold">{{ $item->driver->name }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Số tiền</small>
                            <span class="fw-bold {{ $item->type === 'addition' ? 'text-success' : 'text-danger' }}">
                                {{ $item->type === 'addition' ? '+' : '-' }}{{ number_format($item->amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                        @if($item->note)
                        <div class="col-12 pt-2 border-top">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> {{ Str::limit($item->note, 40) }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-primary">{{ $item->trip_date->format('d/m/Y') }}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('trips.edit', $item) }}"><i class="bi bi-pencil text-primary"></i> Sửa</a></li>
                                <li>
                                    <form method="POST" action="{{ route('trips.destroy', $item) }}" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
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
                            <span class="fw-semibold">{{ $item->vehicle->plate_number }}</span> — {{ $item->material->name }}
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Số chuyến</small>
                            <span class="fw-bold fs-5">{{ intval($item->quantity) }}</span>
                        </div>
                        <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold fs-5">{{ number_format($item->total_price, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="text-center text-muted py-5 card">
            <div class="card-body">Chưa có chuyến xe hay khoản phát sinh nào.</div>
        </div>
    @endforelse
</div>
@endsection
