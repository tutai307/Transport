@extends('layouts.app')
@section('title', 'Thêm tuyến đường')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm tuyến đường mới</h4>
</div>

<form method="POST" action="{{ route('routes.store') }}" style="max-width: 600px;">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mb-3">
        <label for="from_location" class="form-label">Điểm đi <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="from_location" name="from_location"
               value="{{ old('from_location') }}" required autofocus placeholder="VD: Mỏ cát Tân An">
    </div>

    <div class="mb-3">
        <label for="to_location" class="form-label">Điểm đến <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="to_location" name="to_location"
               value="{{ old('to_location') }}" required placeholder="VD: Công trình Quận 7">
    </div>

    <div class="mb-3">
        <label for="distance_km" class="form-label">Khoảng cách (km)</label>
        <input type="number" step="0.1" class="form-control" id="distance_km" name="distance_km"
               value="{{ old('distance_km') }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Lưu</button>
        <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>
</form>
@endsection
