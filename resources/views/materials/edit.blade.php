@extends('layouts.app')
@section('title', 'Sửa vật liệu')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa vật liệu: {{ $material->name }}</h4>
</div>

<form method="POST" action="{{ route('materials.update', $material) }}" style="max-width: 600px;" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Tên vật liệu <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $material->name) }}" required>
        <div class="invalid-feedback">Vui lòng nhập tên vật liệu.</div>
    </div>

    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $material->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Đang sử dụng</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cập nhật</button>
        <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
