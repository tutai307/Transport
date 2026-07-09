{{-- Modal thêm xe nhanh -- include vào trips/create.blade.php --}}
<div class="modal fade" id="quickAddVehicleModal" tabindex="-1" aria-labelledby="quickAddVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddVehicleModalLabel">
                    <i class="bi bi-truck text-primary"></i> Thêm xe mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Biển số xe <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="quickVehiclePlate" placeholder="VD: 51C-123.45">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tài xế mặc định</label>
                    <select class="form-select" id="quickVehicleDriver" data-placeholder="-- Chọn tài xế (tuỳ chọn) --">
                        <option value="">-- Chưa xác định --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Có thể bỏ trống nếu xe mới chưa biết tài xế.</small>
                </div>
                <div id="quickVehicleError" class="alert alert-danger d-none mb-0"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnSaveQuickVehicle">
                    <i class="bi bi-check-circle"></i> Lưu xe
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('quickAddVehicleModal');

    // Select2 riêng cho dropdown tài xế trong modal (modal chỉ init sau khi mở, tránh xung đột z-index)
    $(modalEl).on('shown.bs.modal', function() {
        if (!$('#quickVehicleDriver').hasClass('select2-hidden-accessible')) {
            $('#quickVehicleDriver').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $(modalEl),
                placeholder: $('#quickVehicleDriver').data('placeholder'),
                allowClear: true,
            });
        }
        document.getElementById('quickVehiclePlate').focus();
    });

    modalEl.addEventListener('hidden.bs.modal', function() {
        document.getElementById('quickVehiclePlate').value = '';
        $('#quickVehicleDriver').val('').trigger('change');
        document.getElementById('quickVehicleError').classList.add('d-none');
    });

    document.getElementById('quickVehiclePlate').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnSaveQuickVehicle').click();
        }
    });

    document.getElementById('btnSaveQuickVehicle').addEventListener('click', function() {
        const plateInput = document.getElementById('quickVehiclePlate');
        const plate = plateInput.value.trim();
        const driverId = $('#quickVehicleDriver').val();
        const errorDiv = document.getElementById('quickVehicleError');
        const btn = this;

        if (!plate) {
            errorDiv.textContent = 'Vui lòng nhập biển số xe.';
            errorDiv.classList.remove('d-none');
            return;
        }

        errorDiv.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

        fetch('{{ route("api.vehicles.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ plate_number: plate, default_driver_id: driverId || null }),
        })
        .then(function(r) {
            if (!r.ok) return r.json().then(function(d) { throw d; });
            return r.json();
        })
        .then(function(data) {
            const option = new Option(data.plate_number, data.id, true, true);
            $('#vehicle_id').append(option).trigger('change');

            if (data.default_driver_id) {
                $('#driver_id').val(String(data.default_driver_id)).trigger('change');
            }

            bootstrap.Modal.getInstance(modalEl).hide();
        })
        .catch(function(err) {
            const msg = (err && err.errors && err.errors.plate_number) ? err.errors.plate_number[0]
                : (err && err.message) ? err.message : 'Có lỗi xảy ra, vui lòng thử lại.';
            errorDiv.textContent = msg;
            errorDiv.classList.remove('d-none');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Lưu xe';
        });
    });
});
</script>
@endpush
