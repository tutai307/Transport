@extends('layouts.app')
@section('title', 'Thêm vật liệu')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm vật liệu mới</h4>
</div>

<form method="POST" action="{{ route('materials.store') }}" style="max-width: 600px;">
    @csrf

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
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="VD: Cát, Đá, Đất...">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="unit" class="form-label">Đơn vị tính</label>
                <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', 'm3') }}"
                       placeholder="VD: m3, tấn...">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="import_price" class="form-label">Đơn giá nhập <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="import_price" name="import_price" 
                       value="{{ old('import_price', 0) }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="sell_price" class="form-label">Đơn giá bán <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="sell_price" name="sell_price" 
                       value="{{ old('sell_price', 0) }}" required>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
