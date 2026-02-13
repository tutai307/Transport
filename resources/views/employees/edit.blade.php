@extends('layouts.app')
@section('title', 'Sửa tài xế')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa tài xế: {{ $employee->name }}</h4>
</div>

<form method="POST" action="{{ route('employees.update', $employee) }}" style="max-width: 600px;" class="needs-validation" novalidate>
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
        <label for="name" class="form-label">Tên tài xế <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
        <div class="invalid-feedback">Vui lòng nhập tên tài xế.</div>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
    </div>

    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Đang làm việc</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cập nhật</button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
