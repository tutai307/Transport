@php
    $showProject = empty($filters['project_id']) && empty($filters['hide_project']);
    // Cột: STT, Ngày, [Dự án], Biển số, Tài xế, Vật liệu, Số chuyến, Đơn giá, Thành tiền, Ghi chú
    $totalCols   = $showProject ? 10 : 9;
    $leftCols    = $showProject ? 6 : 5;
@endphp
<table>
    <thead>
        {{-- Administrative Header --}}
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">CÔNG TY XÂY DỰNG PHƯƠNG THẢO</td>
            <td></td>
            <td></td>
            <td colspan="{{ $totalCols - 5 }}" style="font-weight: bold; text-align: center;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Số: ....../BC-PT</td>
            <td></td>
            <td></td>
            <td colspan="{{ $totalCols - 5 }}" style="font-weight: bold; text-align: center; text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</td>
        </tr>
        <tr>
            <td colspan="{{ $totalCols }}" style="height: 20px;"></td>
        </tr>
        {{-- Title --}}
        <tr>
            <td colspan="{{ $totalCols }}" style="font-weight: bold; font-size: 16px; text-align: center; text-transform: uppercase;">
                {{ !empty($filters['driver_id']) ? 'PHIẾU TÍNH LƯƠNG TÀI XẾ' : 'BÁO CÁO TỔNG HỢP CHUYẾN XE VẬN CHUYỂN (CƯỚC PHÍ)' }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $totalCols }}" style="text-align: center; font-style: italic;">
                @if(!empty($filters['driver_id']))
                    Tài xế: {{ \App\Models\Employee::find($filters['driver_id'])?->name }}
                @elseif(isset($filters['project_id']) && $project = \App\Models\Project::find($filters['project_id']))
                    Dự án: {{ $project->name }}
                @endif
                @if(isset($filters['year']) && isset($filters['month']))
                    — Tháng {{ $filters['month'] }}/{{ $filters['year'] }}
                @elseif(isset($filters['date_from']) && isset($filters['date_to']))
                    — Từ ngày {{ date('d/m/Y', strtotime($filters['date_from'])) }} đến ngày {{ date('d/m/Y', strtotime($filters['date_to'])) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ $totalCols }}" style="height: 10px;"></td>
        </tr>
        {{-- Table Headings --}}
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">STT</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ngày</th>
            @if($showProject)
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Dự án</th>
            @endif
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Biển số xe</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Tài xế</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Loại vật liệu</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Số chuyến</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Đơn giá</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Thành tiền</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount        = 0;
            $calculatedTripCount = 0;
        @endphp
        @foreach($allRecords as $index => $item)
            @php
                if (isset($item->is_adjustment) && $item->is_adjustment) {
                    $rowAmount = ($item->type === 'addition') ? $item->amount : -$item->amount;
                    $rowQty    = 0;
                } else {
                    $rowAmount = $item->total_price;
                    $rowQty    = $item->quantity;
                }
                $totalAmount         += $rowAmount;
                $calculatedTripCount += $rowQty;
            @endphp
            @if(isset($item->is_adjustment) && $item->is_adjustment)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; background-color: #FFF2CC;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: center; background-color: #FFF2CC;">{{ $item->trip_date->format('d/m/Y') }}</td>
                    @if($showProject)
                        <td style="border: 1px solid #000000; background-color: #FFF2CC;">{{ $item->project->name }}</td>
                    @endif
                    <td style="border: 1px solid #000000; text-align: center; background-color: #FFF2CC; color: #7f7f7f;">-</td>
                    <td style="border: 1px solid #000000; background-color: #FFF2CC;">{{ $item->driver->name }}</td>
                    <td style="border: 1px solid #000000; background-color: #FFF2CC; font-style: italic; font-weight: bold;">
                        {{ $item->type === 'addition' ? 'Phụ cấp / Chi hộ' : 'Tạm ứng / Khấu trừ' }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; background-color: #FFF2CC; color: #7f7f7f;">-</td>
                    <td style="border: 1px solid #000000; text-align: right; background-color: #FFF2CC; color: #7f7f7f;">-</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold; background-color: #FFF2CC; color: {{ $item->type === 'addition' ? '#2e7d32' : '#c62828' }};">
                        {{ ($item->type === 'addition' ? 1 : -1) * $item->amount }}
                    </td>
                    <td style="border: 1px solid #000000; background-color: #FFF2CC;">{{ $item->note }}</td>
                </tr>
            @else
                <tr>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->trip_date->format('d/m/Y') }}</td>
                    @if($showProject)
                        <td style="border: 1px solid #000000;">{{ $item->project->name }}</td>
                    @endif
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->vehicle_plate_snapshot ?? '—' }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->driver_name_snapshot ?? '—' }}</td>
                    <td style="border: 1px solid #000000;">{{ optional($item->material)->name }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $item->quantity + 0 }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ $item->freight_price }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $item->total_price }}</td>
                    <td style="border: 1px solid #000000;">{{ $item->note }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        {{-- Summary --}}
        <tr>
            <td colspan="{{ $leftCols }}" style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">TỔNG CỘNG</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #FFF2CC;">{{ $calculatedTripCount + 0 }}</td>
            <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">{{ $totalAmount }}</td>
            <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
        </tr>
        <tr>
            <td colspan="{{ $totalCols }}" style="height: 30px;"></td>
        </tr>
        {{-- Signatures --}}
        <tr>
            @for($i = 0; $i < $leftCols; $i++) <td></td> @endfor
            <td colspan="4" style="text-align: center; font-style: italic;">
                Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">Người lập biểu</td>
            @for($i = 0; $i < ($leftCols - 3); $i++) <td></td> @endfor
            <td colspan="4" style="font-weight: bold; text-align: center;">Giám đốc</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-style: italic;">(Ký và ghi rõ họ tên)</td>
            @for($i = 0; $i < ($leftCols - 3); $i++) <td></td> @endfor
            <td colspan="4" style="text-align: center; font-style: italic;">(Ký tên, đóng dấu)</td>
        </tr>
    </tfoot>
</table>
