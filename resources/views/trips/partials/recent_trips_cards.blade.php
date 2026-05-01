@forelse($recentTrips as $trip)
<div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-primary">{{ $trip->trip_date->format('d/m/Y') }}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border">{{ $trip->vehicle->plate_number }}</span>
                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Sửa" target="_blank">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('trips.destroy', $trip) }}" class="d-inline delete-trip-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Xoá">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <small class="text-muted d-block">Tài xế</small>
                <span class="fw-semibold">{{ $trip->driver->name }}</span>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Vật liệu</small>
                <span>{{ $trip->material->name }}</span>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Cung chặng</small>
                <span class="small">{{ $trip->route->from_location }} → {{ $trip->route->to_location }}</span>
            </div>
            <div class="col-4 pt-2 border-top">
                <small class="text-muted d-block">Số chuyến</small>
                <span class="fw-bold">{{ $trip->quantity + 0 }}</span>
            </div>
            <div class="col-4 pt-2 border-top">
                <small class="text-muted d-block">Đơn giá</small>
                <span class="fw-semibold">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</span>
            </div>
            <div class="col-4 pt-2 border-top text-end">
                <small class="text-muted d-block">Thành tiền</small>
                <span class="fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</span>
            </div>
            @if($trip->note)
            <div class="col-12">
                <small class="text-muted"><i class="bi bi-info-circle"></i> {{ Str::limit($trip->note, 40) }}</small>
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="text-center text-muted py-4 card">
    <div class="card-body">Không có chuyến xe nào trong tháng này.</div>
</div>
@endforelse
