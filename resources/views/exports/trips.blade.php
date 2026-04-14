<table>
    <thead>
        {{-- Administrative Header --}}
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">CÔNG TY XÂY DỰNG PHƯƠNG THẢO</td>
            <td></td>
            <td></td>
            <td colspan="{{ $exportType == 'profit' ? '6' : '4' }}" style="font-weight: bold; text-align: center;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Số: ....../BC-PT</td>
            <td></td>
            <td></td>
            <td colspan="{{ $exportType == 'profit' ? '6' : '4' }}" style="font-weight: bold; text-align: center; text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</td>
        </tr>
        <tr>
            <td colspan="{{ $exportType == 'profit' ? '11' : '9' }}" style="height: 20px;"></td>
        </tr>
        {{-- Title --}}
        <tr>
            <td colspan="{{ $exportType == 'profit' ? '11' : '9' }}" style="font-weight: bold; font-size: 16px; text-align: center; text-transform: uppercase;">
                BÁO CÁO TỔNG HỢP CHUYẾN XE VẬN CHUYỂN ({{ $exportType == 'profit' ? 'LỢI NHUẬN' : 'CƯỚC PHÍ' }})
            </td>
        </tr>
        <tr>
            <td colspan="{{ $exportType == 'profit' ? '11' : '9' }}" style="text-align: center; font-style: italic;">
                @if(isset($filters['project_id']) && $project = \App\Models\Project::find($filters['project_id']))
                    Dự án: {{ $project->name }}
                @endif
                @if(isset($filters['year']) && isset($filters['month']))
                    - Tháng {{ $filters['month'] }}/{{ $filters['year'] }}
                @elseif(isset($filters['date_from']) && isset($filters['date_to']))
                    - Từ ngày {{ date('d/m/Y', strtotime($filters['date_from'])) }} đến ngày {{ date('d/m/Y', strtotime($filters['date_to'])) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ $exportType == 'profit' ? '11' : '9' }}" style="height: 10px;"></td>
        </tr>
        {{-- Table Headings --}}
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">STT</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ngày</th>
            @if(empty($filters['project_id']))
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
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Giá cước</th>
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Thành tiền cước</th>
            @endif
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $totalVolume = 0;
            $totalAmount = 0;
            $calculatedTripCount = 0;
        @endphp
        @foreach($trips as $index => $trip)
            @php 
                $totalVolume += $trip->quantity;
                $rowAmount = ($exportType == 'profit') ? $trip->profit : $trip->total_price;
                $totalAmount += $rowAmount;
                $calculatedTripCount += $trip->quantity;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $trip->trip_date->format('d/m/Y') }}</td>
                @if(empty($filters['project_id']))
                    <td style="border: 1px solid #000000;">{{ $trip->project->name }}</td>
                @endif
                <td style="border: 1px solid #000000; text-align: center;">{{ $trip->vehicle->plate_number }}</td>
                <td style="border: 1px solid #000000;">{{ $trip->driver->name }}</td>
                <td style="border: 1px solid #000000;">{{ $trip->material->name }}</td>
                <td style="border: 1px solid #000000; text-align: center; mso-number-format:'\@';">{{ $trip->quantity + 0 }}</td>
                
                @if($exportType == 'profit')
                    <td style="border: 1px solid #000000; text-align: right; mso-number-format:'\@';">{{ number_format($trip->buy_price, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right; mso-number-format:'\@';">{{ number_format($trip->sell_price, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold; mso-number-format:'\@';">{{ number_format($trip->profit, 0, ',', '.') }}</td>
                @else
                    <td style="border: 1px solid #000000; text-align: right; mso-number-format:'\@';">{{ number_format($trip->freight_price, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000000; text-align: right; font-weight: bold; mso-number-format:'\@';">{{ number_format($trip->total_price, 0, ',', '.') }}</td>
                @endif
                
                <td style="border: 1px solid #000000;">{{ $trip->note }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        {{-- Summary Stats --}}
        <tr>
            <td colspan="{{ empty($filters['project_id']) ? 6 : 5 }}" style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">TỔNG CỘNG</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #FFF2CC; mso-number-format:'\@';">{{ $calculatedTripCount + 0 }}</td>
            @if($exportType == 'profit')
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
            @else
                <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
            @endif
            <td style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC; mso-number-format:'\@';">{{ number_format($totalAmount, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
        </tr>
        <tr>
            <td colspan="{{ $exportType == 'profit' ? '11' : '9' }}" style="height: 30px;"></td>
        </tr>
        {{-- Signatures --}}
        <tr>
            @for($i=0; $i < (empty($filters['project_id']) ? 6 : 5); $i++) <td></td> @endfor
            <td colspan="4" style="text-align: center; font-style: italic;">
                Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">Người lập biểu</td>
            @for($i=0; $i < (empty($filters['project_id']) ? 2 : 1); $i++) <td></td> @endfor
            <td colspan="4" style="font-weight: bold; text-align: center;">Giám đốc</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-style: italic;">(Ký và ghi rõ họ tên)</td>
            @for($i=0; $i < (empty($filters['project_id']) ? 2 : 1); $i++) <td></td> @endfor
            <td colspan="4" style="text-align: center; font-style: italic;">(Ký tên, đóng dấu)</td>
        </tr>
    </tfoot>
</table>
