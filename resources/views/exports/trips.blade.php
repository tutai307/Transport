@php
    $showProject = empty($filters['project_id']) && empty($filters['hide_project']);
    $totalCols   = $showProject
        ? ($exportType == 'profit' ? 11 : 10)
        : ($exportType == 'profit' ? 10 : 9);
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
                {{ !empty($filters['driver_id']) ? 'PHIẾU TÍNH LƯƠNG TÀI XẾ' : 'BÁO CÁO TỔNG HỢP CHUYẾN XE VẬN CHUYỂN (' . ($exportType == 'profit' ? 'LỢI NHUẬN' : 'CƯỚC PHÍ') . ')' }}
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
            @if($exportType == 'profit')
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Giá mua</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Giá bán</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Lợi nhuận</th>
            @else
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Đơn giá</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Thành tiền</th>
            @endif
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount        = 0;
            $calculatedTripCount = 0;
        @endphp
        @foreach($trips as $index => $trip)
            @php
                $rowAmount           = ($exportType == 'profit') ? $trip->profit : $trip->total_price;
                $totalAmount        += $rowAmount;
                $calculatedTripCount += $trip->quantity;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $trip->trip_date->format('d/m/Y') }}</td>
                @if($showProject)
                    <td style="border: 1px solid #000000;">{{ $trip->project->name }}</td>
                @endif
                <td style="border: 1px solid #000000; text-align: center;">{{ $trip->vehicle->plate_number }}</td>
                <td style="border: 1px solid #000000;">{{ $trip->driver->name }}</td>
                <td style="border: 1px solid #000000;">{{ $trip->material->name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $trip->quantity + 0 }}</td>

                @if($exportType == 'profit')
                    <td style="border: 1px solid #000000; text-align: right;">{{ intval($trip->buy_price) }}</td>
                    <td style="border: 1px solid #000000; text-align: right;">{{ intval($trip->sell_price) }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ intval($trip->profit) }}</td>
                @else
                    <td style="border: 1px solid #000000; text-align: right;">{{ intval($trip->freight_price) }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ intval($trip->total_price) }}</td>
                @endif

                <td style="border: 1px solid #000000;">{{ $trip->note }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        {{-- Summary --}}
        <tr>
            <td colspan="{{ $leftCols }}" style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">TỔNG CỘNG</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #FFF2CC;">{{ $calculatedTripCount + 0 }}</td>
            @if($exportType == 'profit')
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
            @else
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
            @endif
            <td style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">{{ intval($totalAmount) }}</td>
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
