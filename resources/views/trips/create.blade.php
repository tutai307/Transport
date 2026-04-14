@extends('layouts.app')
@section('title', 'Thêm chuyến xe')

@section('content')
@php
    $selectedProject = $projects->where('id', request('project_id'))->first();
    $pageTitle = $selectedProject ? 'Thêm chuyến xe: ' . $selectedProject->name : 'Thêm chuyến xe mới';
@endphp

<div class="page-header">
    <h4 id="page-main-title"><i class="bi bi-plus-circle"></i> {{ $pageTitle }}</h4>
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
        @if(request('project_id'))
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="trip_date" class="form-label">Ngày <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="trip_date" name="trip_date"
                           value="{{ old('trip_date', request('trip_date', date('Y-m-d'))) }}" required>
                    <div class="invalid-feedback">Vui lòng chọn ngày.</div>
                </div>
                <input type="hidden" name="project_id" id="project_id_select" value="{{ request('project_id') }}">
            </div>
        @else
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
                    <select class="form-select select2" id="project_id_select" name="project_id" required data-placeholder="-- Chọn dự án --">
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
        @endif
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="vehicle_id" class="form-label">Xe <span class="text-danger">*</span></label>
                <select class="form-select select2" id="vehicle_id" name="vehicle_id" required data-placeholder="-- Chọn xe --">
                    <option value="">-- Chọn xe --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}"
                                {{ old('vehicle_id', request('vehicle_id')) == $vehicle->id ? 'selected' : '' }}>
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
                        <option value="{{ $employee->id }}" {{ old('driver_id', request('driver_id')) == $employee->id ? 'selected' : '' }}>
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
                                {{ old('material_id', request('material_id')) == $material->id ? 'selected' : '' }}>
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
                        <option value="{{ $route->id }}" {{ old('route_id', request('route_id')) == $route->id ? 'selected' : '' }}>
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
                       value="{{ old('quantity', 0) }}" required min="0.01">
                <div class="invalid-feedback">Vui lòng nhập số chuyến.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="freight_price" class="form-label">Giá cước/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input" id="freight_price" name="freight_price"
                       value="{{ old('freight_price', request('freight_price', 0)) }}" required>
                <div class="invalid-feedback">Vui lòng nhập giá cước.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="buy_price" class="form-label">Giá mua/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input border-warning" id="buy_price" name="buy_price"
                       value="{{ old('buy_price', request('buy_price', 0)) }}" required>
                <div class="invalid-feedback">Vui lòng nhập giá mua.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="sell_price" class="form-label">Giá bán/chuyến <span class="text-danger">*</span></label>
                <input type="text" class="form-control currency-input border-info" id="sell_price" name="sell_price"
                       value="{{ old('sell_price', request('sell_price', 0)) }}" required>
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
        <textarea class="form-control" id="note" name="note" rows="2">{{ old('note') }}</textarea>
    </div>

    <div class="d-flex gap-2 mb-5">
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

<hr class="my-5">

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
    <h5 class="mb-0 text-primary" id="recent-trips-title"><i class="bi bi-calendar-event"></i> Các chuyến xe đã nhập (Tháng {{ $filterMonth }}/{{ $filterYear }})</h5>
    <div class="d-flex align-items-center gap-2">
        <label class="text-muted small mb-0">Xem tháng khác:</label>
        <select id="filter_month" class="form-select form-select-sm w-auto">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
            @endfor
        </select>
        <select id="filter_year" class="form-select form-select-sm w-auto">
            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>
    </div>
</div>

<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-sm table-hover align-middle shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Ngày</th>
                <th>Xe</th>
                <th>Tài xế</th>
                <th>Vật liệu</th>
                <th class="text-end">Số lượng</th>
                <th class="text-end">Thành tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody id="recent-trips-body">
            @include('trips.partials.recent_trips_rows', ['recentTrips' => $recentTrips])
        </tbody>
    </table>
</div>

{{-- Mobile View (Cards) --}}
<div id="recent-trips-mobile" class="d-block d-md-none">
    @include('trips.partials.recent_trips_cards', ['recentTrips' => $recentTrips])
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tripForm');
    const vehicleSelect = document.getElementById('vehicle_id');
    const materialSelect = document.getElementById('material_id');
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

    // Tính thành tiền (Số lượng x Giá cước)
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

    // --- AJAX Filter logic ---
    const monthSelect = document.getElementById('filter_month');
    const yearSelect = document.getElementById('filter_year');
    const projectSelect = document.getElementById('project_id_select');
    const tableBody = document.getElementById('recent-trips-body');
    const tableTitle = document.getElementById('recent-trips-title');
    const pageMainTitle = document.getElementById('page-main-title');

    function updateRecentTrips() {
        const month = monthSelect.value;
        const year = yearSelect.value;
        const projectId = projectSelect.value; 
        
        // Show loading state
        tableBody.style.opacity = '0.5';
        const mobileContainer = document.getElementById('recent-trips-mobile');
        if (mobileContainer) mobileContainer.style.opacity = '0.5';
        
        let url = `{{ route('trips.create') }}?filter_month=${month}&filter_year=${year}`;
        if (projectId) {
            url += `&project_id=${projectId}`;
        }
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = data.html;
            if (mobileContainer) mobileContainer.innerHTML = data.cards;
            
            tableBody.style.opacity = '1';
            if (mobileContainer) mobileContainer.style.opacity = '1';
            
            // Get project name
            const projectName = projectId ? projectSelect.options[projectSelect.selectedIndex].text : 'Tất cả dự án';
            tableTitle.innerHTML = `<i class="bi bi-calendar-event"></i> Chuyến xe - ${projectName} (Tháng ${month}/${year})`;
            
            // Update page main title
            if (projectId) {
                pageMainTitle.innerHTML = `<i class="bi bi-plus-circle"></i> Thêm chuyến xe: ${projectName}`;
            } else {
                pageMainTitle.innerHTML = `<i class="bi bi-plus-circle"></i> Thêm chuyến xe mới`;
            }
        })
        .catch(error => {
            console.error('Error fetching trips:', error);
            tableBody.style.opacity = '1';
        });
    }

    monthSelect.addEventListener('change', updateRecentTrips);
    yearSelect.addEventListener('change', updateRecentTrips);
    
    // Listen for project change (using jQuery for Select2 compatibility)
    $('#project_id_select').on('change', updateRecentTrips);
});
</script>
@endpush
@endsection
