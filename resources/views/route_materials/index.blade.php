@extends('layouts.app')
@section('title', 'Bảng giá')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-currency-dollar"></i> Bảng giá theo tuyến đường & vật liệu</h4>
</div>

{{-- Form thêm/cập nhật giá --}}
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title">Thêm / Cập nhật giá</h6>
        <form method="POST" action="{{ route('route-materials.store') }}" class="row g-2 align-items-end">
            @csrf

            @if($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="col-md-4">
                <label class="form-label">Tuyến đường</label>
                <select name="route_id" class="form-select" required>
                    <option value="">-- Chọn tuyến --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">
                            {{ $route->from_location }} → {{ $route->to_location }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Vật liệu</label>
                <select name="material_id" class="form-select" required>
                    <option value="">-- Chọn --</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Giá/m³ (đ)</label>
                <input type="number" name="price_per_m3" class="form-control" step="1" min="0" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Bảng giá hiện tại --}}
@foreach($routeMaterials as $routeId => $prices)
    @php $routeInfo = $prices->first()->route; @endphp
    <div class="card mb-3">
        <div class="card-header bg-light fw-bold">
            <i class="bi bi-signpost-2"></i> {{ $routeInfo->from_location }} → {{ $routeInfo->to_location }}
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Vật liệu</th>
                        <th class="text-end">Giá/m³</th>
                        <th style="width:80px">Xoá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prices as $rm)
                        <tr>
                            <td>{{ $rm->material->name }}</td>
                            <td class="text-end fw-bold">{{ number_format($rm->price_per_m3, 0, ',', '.') }} đ</td>
                            <td>
                                <form method="POST" action="{{ route('route-materials.destroy', $rm) }}"
                                      onsubmit="return confirm('Xoá giá này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

@if($routeMaterials->isEmpty())
    <div class="text-center text-muted py-4">
        Chưa có giá nào. Thêm giá bằng form phía trên.
    </div>
@endif
@endsection
