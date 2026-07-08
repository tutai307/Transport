@extends('layouts.app')
@section('title', 'Thêm vật liệu')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm vật liệu mới</h4>
</div>

<form method="POST" action="{{ route('materials.store') }}" style="max-width: 600px;" class="needs-validation" novalidate>
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Tên vật liệu <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus
               placeholder="VD: Cát, Đá, Đất...">
        <div class="invalid-feedback">Vui lòng nhập tên vật liệu.</div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
