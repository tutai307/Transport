@forelse($recentTrips as $trip)
    <tr>
        <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
        <td>{{ $trip->vehicle->plate_number }}</td>
        <td>{{ $trip->driver->name }}</td>
        <td>{{ $trip->material->name }}</td>
        <td class="text-end">{{ $trip->quantity + 0 }}</td>
        <td class="text-end fw-bold text-success">{{ number_format($trip->total_price, 0, ',', '.') }}đ</td>
        <td class="small">{{ Str::limit($trip->note, 30) }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">Chưa có chuyến xe nào trong khoảng thời gian này.</td>
    </tr>
@endforelse
