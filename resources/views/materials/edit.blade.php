@extends('layouts.app')
@section('title', 'Sửa vật liệu')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa vật liệu: {{ $material->name }}</h4>
</div>

<form method="POST" action="{{ route('materials.update', $material) }}" style="max-width: 600px;" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Tên vật liệu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $material->name) }}" required>
                <div class="invalid-feedback">Vui lòng nhập tên vật liệu.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="unit" class="form-label">Đơn vị tính</label>
                <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $material->unit) }}"
                       placeholder="VD: m3, tấn...">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="import_price" class="form-label">Đơn giá nhập <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="import_price" name="import_price" 
                       value="{{ old('import_price', number_format($material->import_price, 0, '', '')) }}" required>
                <div class="invalid-feedback">Vui lòng nhập đơn giá nhập.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="sell_price" class="form-label">Đơn giá bán <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="sell_price" name="sell_price" 
                       value="{{ old('sell_price', number_format($material->sell_price, 0, '', '')) }}" required>
                <div class="invalid-feedback">Vui lòng nhập đơn giá bán.</div>
            </div>
        </div>
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
