@forelse($recentTrips as $trip)
    @if(isset($trip->is_adjustment) && $trip->is_adjustment)
        <tr class="table-warning-subtle">
            <td class="text-nowrap">{{ $trip->trip_date->format('d/m/Y') }}</td>
            <td class="text-muted text-center">-</td>
            <td>{{ $trip->driver->name }}</td>
            <td>
                @if($trip->type === 'addition')
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-plus-circle"></i> Phụ cấp / Chi hộ</span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-dash-circle"></i> Tạm ứng / Trừ</span>
                @endif
            </td>
            <td class="text-muted text-center">-</td>
            <td class="text-muted text-center">-</td>
            <td class="text-muted text-center">-</td>
            <td class="text-end fw-bold {{ $trip->type === 'addition' ? 'text-success' : 'text-danger' }}">
                {{ $trip->type === 'addition' ? '+' : '-' }}{{ number_format($trip->amount, 0, ',', '.') }}đ
            </td>
            <td class="small text-muted">{{ Str::limit($trip->note, 25) }}</td>
            <td class="text-center text-nowrap">
                <form method="POST" action="{{ route('salary-adjustments.destroy', $trip) }}" class="d-inline delete-adjustment-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @else
        <tr>
            <td class="text-nowrap">{{ $trip->trip_date->format('d/m/Y') }}</td>
            <td>{{ $trip->vehicle->plate_number }}</td>
            <td>{{ $trip->driver_name_snapshot ?? optional($trip->driver)->name ?? '—' }}</td>
            <td>{{ $trip->material->name }}</td>
            <td class="small">{{ $trip->route->from_location }} → {{ $trip->route->to_location }}</td>
            <td class="text-end">{{ $trip->quantity + 0 }}</td>
            <td class="text-end">{{ number_format($trip->freight_price, 0, ',', '.') }}đ</td>
            <td class="text-end fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
            <td class="small text-muted">{{ Str::limit($trip->note, 25) }}</td>
            <td class="text-center text-nowrap">
                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary" title="Sửa" target="_blank">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('trips.destroy', $trip) }}" class="d-inline delete-trip-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="10" class="text-center text-muted py-4">Chưa có chuyến xe hay khoản phát sinh nào trong ngày này.</td>
    </tr>
@endforelse
