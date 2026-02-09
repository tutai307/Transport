@extends('layouts.app')
@section('title', 'Thêm xe')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm xe mới</h4>
</div>

<form method="POST" action="{{ route('vehicles.store') }}" style="max-width: 600px;">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="plate_number" class="form-label">Biển số xe <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required autofocus
               placeholder="VD: 51C-123.45">
    </div>

    <div class="mb-3">
        <label for="default_volume_m3" class="form-label">Thể tích mặc định (m³) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control" id="default_volume_m3" name="default_volume_m3"
               value="{{ old('default_volume_m3', 0) }}" required>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
