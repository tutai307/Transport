@extends('layouts.app')
@section('title', 'Thêm dự án')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm dự án mới</h4>
</div>

<form method="POST" action="{{ route('projects.store') }}" style="max-width: 600px;" class="needs-validation" novalidate>
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Tên dự án <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
        <div class="invalid-feedback">Vui lòng nhập tên dự án.</div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
