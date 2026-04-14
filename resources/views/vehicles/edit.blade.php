@extends('layouts.app')
@section('title', 'Sửa xe')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa xe: {{ $vehicle->plate_number }}</h4>
</div>

<form method="POST" action="{{ route('vehicles.update', $vehicle) }}" style="max-width: 600px;" class="needs-validation" novalidate>
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
        <div class="invalid-feedback">Vui lòng nhập biển số xe.</div>
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
