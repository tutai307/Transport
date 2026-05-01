{{-- Modal tạo tuyến đường nhanh -- include vào create.blade.php và edit.blade.php --}}
<div class="modal fade" id="createRouteModal" tabindex="-1" aria-labelledby="createRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRouteModalLabel">
                    <i class="bi bi-plus-circle text-primary"></i> Tạo tuyến đường mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Điểm đi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="createRouteFrom" placeholder="Ví dụ: Mỏ A...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Điểm đến <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="createRouteTo" placeholder="Ví dụ: Công trình B...">
                </div>
                <div id="createRouteError" class="alert alert-danger d-none mb-0"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnSaveRoute">
                    <i class="bi bi-check-circle"></i> Lưu tuyến đường
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hủy init mặc định của layout rồi reinit với tính năng tạo mới
    $('#route_id').select2('destroy');
    $('#route_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn tuyến --',
        allowClear: true,
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);
            if (!term) return null;
            return {
                id: '__create__',
                text: '+ Tạo tuyến mới: "' + term + '"',
                newTag: true,
                newTerm: term
            };
        },
        language: {
            noResults: function() { return "Không tìm thấy kết quả"; }
        }
    });

    // Chặn chọn tag mới → mở modal thay thế
    $('#route_id').on('select2:selecting', function(e) {
        if (!e.params.args.data.newTag) return;

        e.preventDefault();
        $('#route_id').select2('close');

        var term = e.params.args.data.newTerm;
        // Thử tách "Điểm đi → Điểm đến"
        var parts = term.split(/\s*[→>]\s*/);
        document.getElementById('createRouteFrom').value = parts[0] ? parts[0].trim() : '';
        document.getElementById('createRouteTo').value   = parts[1] ? parts[1].trim() : '';
        document.getElementById('createRouteError').classList.add('d-none');

        var modal = new bootstrap.Modal(document.getElementById('createRouteModal'));
        modal.show();

        // Focus vào ô còn trống
        setTimeout(function() {
            var from = document.getElementById('createRouteFrom');
            var to   = document.getElementById('createRouteTo');
            (!from.value ? from : to).focus();
        }, 350);
    });

    // Enter trong modal để lưu nhanh
    ['createRouteFrom', 'createRouteTo'].forEach(function(id) {
        document.getElementById(id).addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnSaveRoute').click();
            }
        });
    });

    // Lưu tuyến đường mới qua AJAX
    document.getElementById('btnSaveRoute').addEventListener('click', function() {
        var from     = document.getElementById('createRouteFrom').value.trim();
        var to       = document.getElementById('createRouteTo').value.trim();
        var errorDiv = document.getElementById('createRouteError');
        var btn      = this;

        if (!from || !to) {
            errorDiv.textContent = 'Vui lòng nhập đầy đủ điểm đi và điểm đến.';
            errorDiv.classList.remove('d-none');
            return;
        }

        errorDiv.classList.add('d-none');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

        fetch('{{ route("api.routes.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ from_location: from, to_location: to })
        })
        .then(function(r) {
            if (!r.ok) return r.json().then(function(d) { throw d; });
            return r.json();
        })
        .then(function(data) {
            // Thêm option mới và chọn nó
            var option = new Option(data.full_name, data.id, true, true);
            $('#route_id').append(option).trigger('change');

            bootstrap.Modal.getInstance(document.getElementById('createRouteModal')).hide();
            document.getElementById('createRouteFrom').value = '';
            document.getElementById('createRouteTo').value   = '';
        })
        .catch(function(err) {
            var msg = (err && err.message) ? err.message : 'Có lỗi xảy ra, vui lòng thử lại.';
            errorDiv.textContent = msg;
            errorDiv.classList.remove('d-none');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Lưu tuyến đường';
        });
    });
});
</script>
@endpush
