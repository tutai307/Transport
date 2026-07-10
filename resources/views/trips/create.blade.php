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
<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="tripFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') !== 'adjustment' ? 'active' : '' }}" id="trip-tab" data-bs-toggle="tab" data-bs-target="#trip-pane" type="button" role="tab" aria-controls="trip-pane" aria-selected="{{ request('tab') !== 'adjustment' ? 'true' : 'false' }}">
            <i class="bi bi-truck"></i> Nhập chuyến xe
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('tab') === 'adjustment' ? 'active' : '' }}" id="adjustment-tab" data-bs-toggle="tab" data-bs-target="#adjustment-pane" type="button" role="tab" aria-controls="adjustment-pane" aria-selected="{{ request('tab') === 'adjustment' ? 'true' : 'false' }}">
            <i class="bi bi-cash-coin"></i> Nhập Phụ cấp / Tạm ứng
        </button>
    </li>
</ul>

<div class="tab-content" id="tripFormTabsContent">
    <!-- Tab Chuyến Xe -->
    <div class="tab-pane fade {{ request('tab') !== 'adjustment' ? 'show active' : '' }}" id="trip-pane" role="tabpanel" aria-labelledby="trip-tab">
        <form method="POST" action="{{ route('trips.store') }}" id="tripForm" class="needs-validation" novalidate>
            @csrf

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
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select select2" id="vehicle_id" name="vehicle_id" required data-placeholder="-- Chọn xe --">
                                <option value="">-- Chọn xe --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id', request('vehicle_id')) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->plate_number }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#quickAddVehicleModal" title="Thêm xe mới">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Vui lòng chọn xe.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Tài xế</label>
                        <select class="form-select select2" id="driver_id" name="driver_id" data-placeholder="-- Chưa xác định tài xế --">
                            <option value="">-- Chưa xác định tài xế --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('driver_id', request('driver_id')) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Có thể bỏ trống nếu xe mới chưa biết tài xế — bổ sung sau.</div>
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
                        <label for="route_id" class="form-label">Cung chặng <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select select2" id="route_id" name="route_id" required data-placeholder="-- Chọn cung chặng --">
                                <option value="">-- Chọn cung chặng --</option>
                                @foreach($selectedRoutes as $route)
                                    <option value="{{ $route->id }}" data-price="{{ (int) $route->price }}"
                                            {{ $selectedRoute && $selectedRoute->id == $route->id ? 'selected' : '' }}>
                                        {{ $route->from_location }} → {{ $route->to_location }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="selected_route_ids" id="selected_route_ids" value="{{ implode(',', $selectedRouteIds) }}">
                            <button type="button" class="btn btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#routePriceModal">
                                <i class="bi bi-signpost-split"></i> Danh sách cung chặng
                            </button>
                        </div>
                        <div class="invalid-feedback">Vui lòng chọn cung chặng.</div>
                        <div class="form-text">Bấm "Danh sách cung chặng" để chọn các cung chặng sẽ dùng cho ngày này, sau đó chọn 1 cung chặng ở ô trên.</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số chuyến <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control" id="quantity" name="quantity"
                               value="{{ old('quantity', 0) }}" required min="1">
                        <div class="invalid-feedback">Vui lòng nhập số chuyến (số nguyên dương).</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="freight_price" class="form-label">Giá cước/chuyến <span class="text-danger">*</span></label>
                        <input type="text" class="form-control currency-input" id="freight_price" name="freight_price"
                               value="{{ old('freight_price', request('freight_price', 0)) }}" required>
                        <div class="invalid-feedback">Vui lòng nhập giá cước.</div>
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
                    <i class="bi bi-check-circle"></i> Lưu chuyến xe
                </button>
                <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Lưu & Thêm mới
                </button>
                <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    <!-- Tab Phụ Cấp / Tạm Ứng -->
    <div class="tab-pane fade {{ request('tab') === 'adjustment' ? 'show active' : '' }}" id="adjustment-pane" role="tabpanel" aria-labelledby="adjustment-tab">
        <form method="POST" action="{{ route('salary-adjustments.store') }}" id="adjustmentForm" class="needs-validation" novalidate>
            @csrf

            <div class="row">
                @if(request('project_id'))
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="adj_trip_date" class="form-label">Ngày <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="adj_trip_date" name="trip_date"
                                   value="{{ old('trip_date', request('trip_date', date('Y-m-d'))) }}" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày.</div>
                        </div>
                        <input type="hidden" name="project_id" id="adj_project_id_hidden" value="{{ request('project_id') }}">
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adj_trip_date" class="form-label">Ngày <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="adj_trip_date" name="trip_date"
                                   value="{{ old('trip_date', request('trip_date', date('Y-m-d'))) }}" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adj_project_id_select" class="form-label">Dự án <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="adj_project_id_select" name="project_id" required data-placeholder="-- Chọn dự án --">
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
                        <label for="adj_driver_id" class="form-label">Tài xế <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="adj_driver_id" name="driver_id" required data-placeholder="-- Chọn tài xế --">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="adj_type" class="form-label">Loại phát sinh <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="adj_type" name="type" required data-placeholder="-- Chọn loại phát sinh --">
                            <option value=""></option>
                            <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>Cộng thêm (Đổ xăng dầu, chi hộ...)</option>
                            <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>Trừ bớt (Tạm ứng trước...)</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn loại phát sinh.</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="adj_amount" class="form-label">Số tiền (VND) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control currency-input fw-bold text-primary" id="adj_amount" name="amount"
                               value="{{ old('amount', 0) }}" required>
                        <div class="invalid-feedback">Vui lòng nhập số tiền phát sinh.</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="adj_note" class="form-label">Ghi chú phát sinh <span class="text-danger">*</span></label>
                <textarea class="form-control" id="adj_note" name="note" rows="2" required placeholder="Ghi chi tiết lý do phát sinh (Ví dụ: Tài xế tự đổ xăng, ứng lương tháng...)">{{ old('note') }}</textarea>
                <div class="invalid-feedback">Vui lòng nhập ghi chú phát sinh.</div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Lưu phát sinh
                </button>
                <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Lưu & Thêm mới
                </button>
                <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

<hr class="my-5">

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
    <h5 class="mb-0 text-primary" id="recent-trips-title"><i class="bi bi-calendar-event"></i> Các chuyến xe đã nhập (Ngày {{ \Carbon\Carbon::parse($filterDate)->format('d/m/Y') }})</h5>
    <small class="text-muted">Danh sách tự cập nhật theo ngày đang chọn ở form phía trên.</small>
</div>

<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-sm table-hover align-middle shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Ngày</th>
                <th>Xe</th>
                <th>Tài xế</th>
                <th>Vật liệu</th>
                <th>Cung chặng</th>
                <th class="text-end">Số chuyến</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
                <th>Ghi chú</th>
                <th style="width:50px"></th>
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

@include('trips.partials.route_price_modal')
@include('trips.partials.vehicle_create_modal')

<!-- Modal Xác nhận Xóa Phát Sinh Lương -->
<div class="modal fade" id="deleteAdjustmentModal" tabindex="-1" aria-labelledby="deleteAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAdjustmentModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i> Xác nhận xóa khoản phát sinh
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <p class="mb-0 fs-5 text-dark">Bạn có chắc chắn muốn xóa khoản phụ cấp / tạm ứng này?</p>
                <small class="text-danger d-block mt-2">Hành động này sẽ cập nhật lại quỹ lương tháng của tài xế và không thể hoàn tác.</small>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger px-4" id="btnConfirmDeleteAdjustment">Xóa</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tripForm');
    const quantityInput = document.getElementById('quantity');
    const freightInput = document.getElementById('freight_price');
    const totalDisplay = document.getElementById('total_price_display');

    // Bootstrap validation logic
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Auto-fill giá cước khi người dùng chọn 1 cung chặng ở select (giá lấy từ danh sách đã chọn trong modal)
    $('#route_id').on('select2:select', function() {
        const price = $(this).find(':selected').data('price');
        if (price !== undefined && price !== '') {
            freightInput.value = String(price);
            freightInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    // Tính thành tiền (Số lượng x Giá cước)
    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const freight = parseFloat(freightInput.value.replace(/\./g, '')) || 0;

        const total = quantity * freight;
        totalDisplay.value = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    quantityInput.addEventListener('input', calculateTotal);
    freightInput.addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();

    // Auto-fill tài xế mặc định khi chọn xe.
    // Người dùng vẫn có thể override.
    $('#vehicle_id').on('select2:select change', function() {
        const vehicleId = $(this).val();
        if (!vehicleId) return;

        fetch(`/api/vehicles/${vehicleId}/default-driver`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            if (data.driver_id) {
                $('#driver_id').val(String(data.driver_id)).trigger('change');
            }
        })
        .catch(() => { /* silent — user vẫn có thể chọn tài xế thủ công */ });
    });

    // Auto-clear số 0 khi focus vào số chuyến
    quantityInput.addEventListener('focus', function() {
        if (this.value === '0') this.value = '';
    });
    quantityInput.addEventListener('blur', function() {
        if (this.value === '') this.value = '0';
    });

    // Initial calculation
    calculateTotal();

    // --- AJAX Filter logic (theo ngày đang chọn ở form, không phải theo tháng) ---
    const projectSelect = document.getElementById('project_id_select');
    const tableBody = document.getElementById('recent-trips-body');
    const tableTitle = document.getElementById('recent-trips-title');
    const pageMainTitle = document.getElementById('page-main-title');

    function currentFilterDate() {
        return tripDateEl.value || '{{ date('Y-m-d') }}';
    }

    function formatDateVn(dateStr) {
        const parts = dateStr.split('-');
        return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : dateStr;
    }

    function updateRecentTrips() {
        const filterDate = currentFilterDate();
        const projectId = projectSelect.value;

        // Show loading state
        tableBody.style.opacity = '0.5';
        const mobileContainer = document.getElementById('recent-trips-mobile');
        if (mobileContainer) mobileContainer.style.opacity = '0.5';

        let url = `{{ route('trips.create') }}?filter_date=${filterDate}`;
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
            tableTitle.innerHTML = `<i class="bi bi-calendar-event"></i> Chuyến xe - ${projectName} (Ngày ${formatDateVn(filterDate)})`;

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

    // Khi đổi ngày → tự cập nhật bảng bên dưới theo ngày đó
    // Dùng Flatpickr instance vì altInput:true làm sự kiện "change" không đáng tin
    const tripDateEl = document.getElementById('trip_date');
    const fpInstance = tripDateEl && tripDateEl._flatpickr;
    if (fpInstance) {
        fpInstance.config.onChange.push(function() {
            updateRecentTrips();
        });
    } else {
        // Fallback nếu Flatpickr chưa init (không dùng altInput)
        tripDateEl.addEventListener('change', updateRecentTrips);
    }

    // Listen for project change (using jQuery for Select2 compatibility)
    $('#project_id_select').on('change', updateRecentTrips);

    // Xoá chuyến xe ngay trong bảng (AJAX) — dùng event delegation vì rows được inject động
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.delete-trip-form');
        if (!form) return;

        e.preventDefault();
        if (!confirm('Xoá chuyến xe này?')) return;

        const btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: '_method=DELETE',
        })
        .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
        .then(function() { updateRecentTrips(); })
        .catch(function() {
            window.showToast('Có lỗi xảy ra khi xoá.', 'error');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-trash"></i>'; }
        });
    });

    // Xoá phát sinh lương dạng modal (AJAX)
    let pendingDeleteAdjustmentForm = null;
    const deleteAdjModalEl = document.getElementById('deleteAdjustmentModal');
    let deleteAdjModal = null;
    if (deleteAdjModalEl) {
        deleteAdjModal = new bootstrap.Modal(deleteAdjModalEl);
    }

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.delete-adjustment-form');
        if (!form) return;

        e.preventDefault();
        pendingDeleteAdjustmentForm = form;
        if (deleteAdjModal) {
            deleteAdjModal.show();
        }
    });

    const confirmDeleteAdjBtn = document.getElementById('btnConfirmDeleteAdjustment');
    if (confirmDeleteAdjBtn) {
        confirmDeleteAdjBtn.addEventListener('click', function() {
            if (!pendingDeleteAdjustmentForm) return;
            
            const form = pendingDeleteAdjustmentForm;
            pendingDeleteAdjustmentForm = null;
            if (deleteAdjModal) {
                deleteAdjModal.hide();
            }

            const btn = form.querySelector('button[type="submit"]');
            if (btn) { 
                btn.disabled = true; 
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; 
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: '_method=DELETE',
            })
            .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function() { updateRecentTrips(); })
            .catch(function() {
                window.showToast('Có lỗi xảy ra khi xoá.', 'error');
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-trash"></i>'; }
            });
        });
    }
});
</script>
@endpush
@endsection
