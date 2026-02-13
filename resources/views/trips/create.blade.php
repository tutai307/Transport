@extends('layouts.app')
@section('title', 'Thêm chuyến xe')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-plus-circle"></i> Thêm chuyến xe mới</h4>
</div>

<form method="POST" action="{{ route('trips.store') }}" id="tripForm" class="needs-validation" novalidate>
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="trip_date" class="form-label">Ngày <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="trip_date" name="trip_date"
                       value="{{ old('trip_date', request('trip_date', date('Y-m-d'))) }}" required>
                <div class="invalid-feedback">Vui lòng chọn ngày.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="project_id" class="form-label">Dự án <span class="text-danger">*</span></label>
                <select class="form-select select2" id="project_id" name="project_id" required data-placeholder="-- Chọn dự án --">
                    <option value="">-- Chọn dự án --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn dự án.</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="vehicle_id" class="form-label">Xe <span class="text-danger">*</span></label>
                <select class="form-select select2" id="vehicle_id" name="vehicle_id" required data-placeholder="-- Chọn xe --">
                    <option value="">-- Chọn xe --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-volume="{{ $vehicle->default_volume_m3 }}"
                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->plate_number }} ({{ $vehicle->default_volume_m3 }} m³)
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn xe.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="driver_id" class="form-label">Tài xế <span class="text-danger">*</span></label>
                <select class="form-select select2" id="driver_id" name="driver_id" required data-placeholder="-- Chọn tài xế --">
                    <option value="">-- Chọn tài xế --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('driver_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn tài xế.</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="material_id" class="form-label">Vật liệu <span class="text-danger">*</span></label>
                <select class="form-select select2" id="material_id" name="material_id" required data-placeholder="-- Chọn vật liệu --">
                    <option value="">-- Chọn vật liệu --</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" data-price="{{ (int)$material->sell_price }}"
                                {{ old('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn vật liệu.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="route_id" class="form-label">Tuyến đường <span class="text-danger">*</span></label>
                <select class="form-select select2" id="route_id" name="route_id" required data-placeholder="-- Chọn tuyến --">
                    <option value="">-- Chọn tuyến --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                            {{ $route->from_location }} → {{ $route->to_location }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn tuyến đường.</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="volume_m3" class="form-label">Khối lượng (m³) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="volume_m3" name="volume_m3"
                       value="{{ old('volume_m3', 0) }}" required min="0.01">
                <div class="invalid-feedback">Vui lòng nhập khối lượng (lớn hơn 0).</div>
                <small class="text-muted">Tự động điền khi chọn xe</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="price_per_m3" class="form-label">Đơn giá/m³ <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="price_per_m3" name="price_per_m3"
                       value="{{ old('price_per_m3', 0) }}" required>
                <div class="invalid-feedback">Vui lòng nhập đơn giá.</div>
                <small class="text-muted">Mặc định lấy theo giá bán vật liệu</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="total_price_display" class="form-label">Thành tiền</label>
                <input type="text" class="form-control bg-light fw-bold text-success" id="total_price_display"
                       readonly>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="note" class="form-label">Ghi chú</label>
        <textarea class="form-control" id="note" name="note" rows="2">{{ old('note') }}</textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle"></i> Lưu
        </button>
        <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg">
            <i class="bi bi-plus-circle"></i> Lưu & Thêm mới
        </button>
        <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tripForm');
    const vehicleSelect = document.getElementById('vehicle_id');
    const materialSelect = document.getElementById('material_id');
    const volumeInput = document.getElementById('volume_m3');
    const priceInput = document.getElementById('price_per_m3');
    const totalDisplay = document.getElementById('total_price_display');

    // Bootstrap validation logic
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Auto-fill volume khi chọn xe
    $('#vehicle_id').on('select2:select', function(e) {
        const selected = e.params.data.element;
        if (selected.dataset.volume) {
            volumeInput.value = selected.dataset.volume;
            calculateTotal();
        }
    });

    // Tính thành tiền
    function calculateTotal() {
        const volume = parseFloat(volumeInput.value) || 0;
        // Parse giá tiền bỏ dấu chấm
        const price = parseFloat(priceInput.value.replace(/\./g, '')) || 0;
        
        const total = volume * price;
        totalDisplay.value = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    volumeInput.addEventListener('input', calculateTotal);
    priceInput.addEventListener('input', calculateTotal);

    // Auto-fill giá khi chọn vật liệu
    $('#material_id').on('select2:select', function(e) {
        const selected = e.params.data.element;
        if (selected.dataset.price) {
            priceInput.value = selected.dataset.price;
            // Format lại giá tiền
            if (typeof formatCurrency === 'function') {
                formatCurrency(priceInput);
            }
            calculateTotal();
        }
    });

    // Initial calculation
    calculateTotal();
});
</script>
@endpush
@endsection
