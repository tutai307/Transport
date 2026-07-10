{{-- Modal chọn danh sách cung chặng dùng cho ngày này -- include vào trips/create.blade.php --}}
<div class="modal fade" id="routePriceModal" tabindex="-1" aria-labelledby="routePriceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routePriceModalLabel">
                    <i class="bi bi-signpost-split text-primary"></i> Danh sách cung chặng dùng cho ngày này
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Tick chọn các cung chặng sẽ xuất hiện trong ô "Cung chặng" của form (kèm giá cước tương ứng), rồi bấm Xác nhận.</p>
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px"></th>
                                <th>Cung chặng</th>
                                <th style="width:180px" class="text-end">Giá cước</th>
                                <th style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody id="routePriceTableBody">
                            @foreach($routes as $route)
                                <tr data-route-id="{{ $route->id }}" data-from="{{ $route->from_location }}" data-to="{{ $route->to_location }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input route-select-checkbox"
                                               {{ in_array($route->id, $selectedRouteIds ?? []) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $route->from_location }} → {{ $route->to_location }}</td>
                                    <td class="text-end">
                                        <span class="price-display">{{ number_format($route->price, 0, ',', '.') }}đ</span>
                                        <input type="text" class="form-control form-control-sm text-end price-input d-none" value="{{ (int) $route->price }}">
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-price" title="Sửa giá"><i class="bi bi-pencil"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-success btn-save-price d-none" title="Lưu giá"><i class="bi bi-check-lg"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <label class="form-label small mb-2 fw-semibold">+ Thêm cung chặng mới</label>
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control form-control-sm" id="newRouteFrom" placeholder="Điểm đi">
                    </div>
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control form-control-sm" id="newRouteTo" placeholder="Điểm đến">
                    </div>
                    <div class="col-8 col-md-3">
                        <input type="text" class="form-control form-control-sm text-end" id="newRoutePrice" placeholder="Giá cước" value="0">
                    </div>
                    <div class="col-4 col-md-1">
                        <button type="button" class="btn btn-success btn-sm w-100" id="btnAddRoutePrice" title="Thêm cung chặng">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnConfirmRoutes">
                    <i class="bi bi-check-circle"></i> Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('routePriceModal');
    const tbody = document.getElementById('routePriceTableBody');
    const routeSelect = document.getElementById('route_id');
    const freightInput = document.getElementById('freight_price');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function formatVnd(n) {
        return new Intl.NumberFormat('vi-VN').format(n);
    }

    function parseVnd(str) {
        return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
    }

    // Format các ô nhập giá (kể cả những ô được thêm động sau này) khi gõ
    document.addEventListener('input', function(e) {
        if (!e.target.matches('#routePriceModal .price-input, #newRoutePrice')) return;
        const cursorFromEnd = e.target.value.length - e.target.selectionStart;
        e.target.value = formatVnd(parseVnd(e.target.value));
        const pos = Math.max(0, e.target.value.length - cursorFromEnd);
        e.target.setSelectionRange(pos, pos);
    });

    // Xác nhận: build lại danh sách option của select "Cung chặng" chỉ gồm các dòng đã tick
    document.getElementById('btnConfirmRoutes').addEventListener('click', function() {
        const checkedRows = Array.from(tbody.querySelectorAll('.route-select-checkbox:checked'))
            .map(cb => cb.closest('tr'));

        if (checkedRows.length === 0) {
            window.showToast('Vui lòng tick chọn ít nhất 1 cung chặng.', 'error');
            return;
        }

        const currentValue = routeSelect.value;
        let options = '<option value="">-- Chọn cung chặng --</option>';
        const checkedIds = [];
        checkedRows.forEach(function(row) {
            const id = row.dataset.routeId;
            const label = `${row.dataset.from} → ${row.dataset.to}`;
            const price = parseVnd(row.querySelector('.price-input').value);
            const selectedAttr = id === currentValue ? 'selected' : '';
            options += `<option value="${id}" data-price="${price}" ${selectedAttr}>${label}</option>`;
            checkedIds.push(id);
        });

        $(routeSelect).html(options).trigger('change');

        // Đồng bộ hidden input để giữ danh sách cung chặng qua "Lưu & Thêm mới"
        const hiddenInput = document.getElementById('selected_route_ids');
        if (hiddenInput) hiddenInput.value = checkedIds.join(',');

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.hide();
    });

    tbody.addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (!row) return;
        const routeId = row.dataset.routeId;

        if (e.target.closest('.btn-edit-price')) {
            row.querySelector('.price-display').classList.add('d-none');
            row.querySelector('.price-input').classList.remove('d-none');
            row.querySelector('.btn-edit-price').classList.add('d-none');
            row.querySelector('.btn-save-price').classList.remove('d-none');
            row.querySelector('.price-input').focus();
            return;
        }

        if (e.target.closest('.btn-save-price')) {
            const btn = e.target.closest('.btn-save-price');
            const input = row.querySelector('.price-input');
            const newPrice = parseVnd(input.value);

            btn.disabled = true;
            fetch(`/api/routes/${routeId}/price`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ price: newPrice }),
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(function(data) {
                row.querySelector('.price-display').textContent = formatVnd(data.price) + 'đ';
                input.value = data.price;
                row.querySelector('.price-display').classList.remove('d-none');
                input.classList.add('d-none');
                row.querySelector('.btn-edit-price').classList.remove('d-none');
                row.querySelector('.btn-save-price').classList.add('d-none');

                // Nếu đang là cung chặng đã chọn cho chuyến xe → cập nhật luôn giá cước + data-price của option
                if (routeSelect.value === routeId) {
                    freightInput.value = String(data.price);
                    freightInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                const option = routeSelect.querySelector(`option[value="${routeId}"]`);
                if (option) option.dataset.price = data.price;
            })
            .catch(function() {
                window.showToast('Có lỗi xảy ra khi lưu giá cước.', 'error');
            })
            .finally(function() {
                btn.disabled = false;
            });
            return;
        }
    });

    document.getElementById('btnAddRoutePrice').addEventListener('click', function() {
        const fromInput = document.getElementById('newRouteFrom');
        const toInput = document.getElementById('newRouteTo');
        const priceInput = document.getElementById('newRoutePrice');
        const from = fromInput.value.trim();
        const to = toInput.value.trim();
        const price = parseVnd(priceInput.value);
        const btn = this;

        if (!from || !to) {
            window.showToast('Vui lòng nhập đầy đủ điểm đi và điểm đến.', 'error');
            return;
        }

        btn.disabled = true;

        fetch('{{ route("api.routes.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ from_location: from, to_location: to, price: price }),
        })
        .then(function(r) {
            if (!r.ok) return r.json().then(function(d) { throw d; });
            return r.json();
        })
        .then(function(data) {
            const tr = document.createElement('tr');
            tr.dataset.routeId = data.id;
            tr.dataset.from = from;
            tr.dataset.to = to;
            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="form-check-input route-select-checkbox" checked>
                </td>
                <td>${data.full_name}</td>
                <td class="text-end">
                    <span class="price-display">${formatVnd(data.price)}đ</span>
                    <input type="text" class="form-control form-control-sm text-end price-input d-none" value="${data.price}">
                </td>
                <td class="text-center text-nowrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-price" title="Sửa giá"><i class="bi bi-pencil"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-success btn-save-price d-none" title="Lưu giá"><i class="bi bi-check-lg"></i></button>
                </td>`;
            tbody.appendChild(tr);

            fromInput.value = '';
            toInput.value = '';
            priceInput.value = '0';
        })
        .catch(function(err) {
            window.showToast((err && err.message) ? err.message : 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
        })
        .finally(function() {
            btn.disabled = false;
        });
    });
});
</script>
@endpush
