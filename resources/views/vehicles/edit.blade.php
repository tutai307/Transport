@extends('layouts.app')
@section('title', 'Sửa xe')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa xe: {{ $vehicle->plate_number }}</h4>
</div>

<form method="POST" action="{{ route('vehicles.update', $vehicle) }}" style="max-width: 600px;">
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
        <label for="plate_number" class="form-label">Biển số xe <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="plate_number" name="plate_number"
               value="{{ old('plate_number', $vehicle->plate_number) }}" required>
    </div>

    <div class="mb-3">
        <label for="default_volume_m3" class="form-label">Thể tích mặc định (m³) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control" id="default_volume_m3" name="default_volume_m3"
               value="{{ old('default_volume_m3', $vehicle->default_volume_m3) }}" required>
    </div>

    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $vehicle->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Đang sử dụng</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cập nhật</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
