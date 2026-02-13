@extends('layouts.app')
@section('title', 'Sửa tuyến đường')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa tuyến đường</h4>
</div>

<form method="POST" action="{{ route('routes.update', $route) }}" style="max-width: 600px;" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="from_location" class="form-label">Điểm đi <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="from_location" name="from_location"
               value="{{ old('from_location', $route->from_location) }}" required>
        <div class="invalid-feedback">Vui lòng nhập điểm đi.</div>
    </div>

    <div class="mb-3">
        <label for="to_location" class="form-label">Điểm đến <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="to_location" name="to_location"
               value="{{ old('to_location', $route->to_location) }}" required>
        <div class="invalid-feedback">Vui lòng nhập điểm đến.</div>
    </div>

    <div class="mb-3">
        <label for="distance_km" class="form-label">Khoảng cách (km)</label>
        <input type="number" step="0.1" class="form-control" id="distance_km" name="distance_km"
               value="{{ old('distance_km', $route->distance_km) }}">
    </div>

    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Đang sử dụng</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cập nhật</button>
        <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
