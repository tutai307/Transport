@extends('layouts.app')
@section('title', 'Thêm tài xế')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm tài xế mới</h4>
</div>

<form method="POST" action="{{ route('employees.store') }}" style="max-width: 600px;">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Tên tài xế <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
