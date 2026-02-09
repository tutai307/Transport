@extends('layouts.app')
@section('title', 'Sửa dự án')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa dự án: {{ $project->name }}</h4>
</div>

<form method="POST" action="{{ route('projects.update', $project) }}" style="max-width: 600px;">
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
        <label for="name" class="form-label">Tên dự án <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $project->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $project->description) }}</textarea>
    </div>

    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Đang hoạt động</label>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Cập nhật</button>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
