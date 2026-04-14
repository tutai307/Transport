@forelse($recentTrips as $trip)
<div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-primary">{{ $trip->trip_date->format('d/m/Y') }}</span>
            <span class="badge bg-light text-dark border">{{ $trip->vehicle->plate_number }}</span>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <small class="text-muted d-block">Vật liệu</small>
                <span class="fw-semibold">{{ $trip->material->name }}</span>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block">Số lượng</small>
                <span class="fw-bold fs-5">{{ $trip->quantity + 0 }}</span>
            </div>
            <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                <span class="text-success fw-bold">{{ number_format($trip->total_price, 0, ',', '.') }} đ</span>
                @if($trip->note)
                    <small class="text-muted"><i class="bi bi-info-circle"></i> {{ Str::limit($trip->note, 20) }}</small>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center text-muted py-4 card">
    <div class="card-body">Không có chuyến xe nào trong tháng này.</div>
</div>
@endforelse
