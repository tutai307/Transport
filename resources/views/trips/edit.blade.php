@extends('layouts.app')
@section('title', 'Sửa chuyến xe')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil"></i> Sửa chuyến xe #{{ $trip->id }}</h4>
</div>

<form method="POST" action="{{ route('trips.update', $trip) }}" id="tripForm" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

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
                       value="{{ old('trip_date', $trip->trip_date->format('Y-m-d')) }}" required>
                <div class="invalid-feedback">Vui lòng chọn ngày.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="project_id" class="form-label">Dự án <span class="text-danger">*</span></label>
                <select class="form-select select2" id="project_id" name="project_id" required data-placeholder="-- Chọn dự án --">
                    <option value="">-- Chọn dự án --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $trip->project_id) == $project->id ? 'selected' : '' }}>
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
                        <option value="{{ $vehicle->id }}"
                                {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->plate_number }}
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
                        <option value="{{ $employee->id }}" {{ old('driver_id', $trip->driver_id) == $employee->id ? 'selected' : '' }}>
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
                        <option value="{{ $material->id }}" 
                                data-price="{{ (int)$material->sell_price }}" 
                                data-import-price="{{ (int)$material->import_price }}" 
                                {{ old('material_id', $trip->material_id) == $material->id ? 'selected' : '' }}>
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
                        <option value="{{ $route->id }}" {{ old('route_id', $trip->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->from_location }} → {{ $route->to_location }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Vui lòng chọn tuyến đường.</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label for="quantity" class="form-label">Số chuyến <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="quantity" name="quantity"
                       value="{{ old('quantity', $trip->quantity) }}" required min="0.01">
                <div class="invalid-feedback">Vui lòng nhập số chuyến.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="freight_price" class="form-label">Giá cước/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="freight_price" name="freight_price"
                       value="{{ old('freight_price', number_format($trip->freight_price, 0, '', '')) }}" required>
                <div class="invalid-feedback">Vui lòng nhập giá cước.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="buy_price" class="form-label">Giá mua/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input border-warning" id="buy_price" name="buy_price"
                       value="{{ old('buy_price', number_format($trip->buy_price, 0, '', '')) }}" required>
                <div class="invalid-feedback">Vui lòng nhập giá mua.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="sell_price" class="form-label">Giá bán/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input border-info" id="sell_price" name="sell_price"
                       value="{{ old('sell_price', number_format($trip->sell_price, 0, '', '')) }}" required>
                <div class="invalid-feedback">Vui lòng nhập giá bán.</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="total_price_display" class="form-label">Thành tiền (Số lượng x Giá cước)</label>
                <input type="text" class="form-control bg-light fw-bold text-success fs-5" id="total_price_display"
                       readonly placeholder="0 đ">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="note" class="form-label">Ghi chú</label>
        <textarea class="form-control" id="note" name="note" rows="2">{{ old('note', $trip->note) }}</textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle"></i> Cập nhật
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
    const quantityInput = document.getElementById('quantity');
    const freightInput = document.getElementById('freight_price');
    const buyInput = document.getElementById('buy_price');
    const sellInput = document.getElementById('sell_price');
    const totalDisplay = document.getElementById('total_price_display');

    // Bootstrap validation logic
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
    const vehicleSelect = document.getElementById('vehicle_id');
    const materialSelect = document.getElementById('material_id');

    // Auto-fill volume khi chọn xe
    $('#vehicle_id').on('select2:select', function(e) {
        const selected = e.params.data.element;
        if (selected.dataset.volume) {
            volumeInput.value = selected.dataset.volume;
            calculateTotal();
        }
    });

    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const freight = parseFloat(freightInput.value.replace(/\./g, '')) || 0;
        const total = quantity * freight;
        totalDisplay.value = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    quantityInput.addEventListener('input', calculateTotal);
    freightInput.addEventListener('input', calculateTotal);
    buyInput.addEventListener('input', calculateTotal);
    sellInput.addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();

    // Auto-fill giá khi chọn vật liệu
    $('#material_id').on('select2:select', function(e) {
        const selected = e.params.data.element;
        if (selected.dataset.price) {
            sellInput.value = selected.dataset.price;
        }
        if (selected.dataset.importPrice) {
            buyInput.value = selected.dataset.importPrice;
        }
        // Format lại giá tiền
        if (typeof formatCurrency === 'function') {
            formatCurrency(sellInput);
            formatCurrency(buyInput);
        }
        calculateTotal();
    });

    // Initial calculation
    calculateTotal();
});
</script>
@endpush
@endsection
