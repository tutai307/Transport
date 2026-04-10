<table>
    <thead>
        {{-- Administrative Header --}}
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">CÔNG TY XÂY DỰNG PHƯƠNG THẢO</td>
            <td></td>
            <td></td>
            <td colspan="4" style="font-weight: bold; text-align: center;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Số: ....../BC-PT</td>
            <td></td>
            <td></td>
            <td colspan="4" style="font-weight: bold; text-align: center; text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</td>
        </tr>
        <tr>
            <td colspan="9" style="height: 20px;"></td>
        </tr>
        {{-- Title --}}
        <tr>
            <td colspan="9" style="font-weight: bold; font-size: 16px; text-align: center; text-transform: uppercase;">
                BÁO CÁO TỔNG HỢP CHUYẾN XE VẬN CHUYỂN
            </td>
        </tr>
        <tr>
            <td colspan="9" style="text-align: center; font-style: italic;">
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
            <td colspan="9" style="height: 10px;"></td>
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
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Tuyến đường</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Khối lượng (m³)</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Thành tiền (VNĐ)</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #E2EFDA;">Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $totalVolume = 0;
            $totalPrice = 0;
            $count = 0;
        @endphp
        @foreach($trips as $index => $trip)
            @php 
                $totalVolume += $trip->volume_m3;
                $totalPrice += $trip->total_price;
                $count++;
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
                <td style="border: 1px solid #000000;">{{ $trip->route->full_name }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($trip->volume_m3, 2) }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ number_format($trip->total_price, 0, ',', '.') }}</td>
                <td style="border: 1px solid #000000;">{{ $trip->note }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        {{-- Summary Stats --}}
        <tr>
            <td colspan="{{ empty($filters['project_id']) ? 7 : 6 }}" style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">TỔNG CỘNG ({{ $count }} chuyến)</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">{{ number_format($totalVolume, 2) }}</td>
            <td style="font-weight: bold; border: 1px solid #000000; text-align: right; background-color: #FFF2CC;">{{ number_format($totalPrice, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000000; background-color: #FFF2CC;"></td>
        </tr>
        <tr>
            <td colspan="9" style="height: 30px;"></td>
        </tr>
        {{-- Signatures --}}
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="4" style="text-align: center; font-style: italic;">
                Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: center;">Người lập biểu</td>
            <td></td>
            <td></td>
            <td colspan="4" style="font-weight: bold; text-align: center;">Giám đốc</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-style: italic;">(Ký và ghi rõ họ tên)</td>
            <td></td>
            <td></td>
            <td colspan="4" style="text-align: center; font-style: italic;">(Ký tên, đóng dấu)</td>
        </tr>
        @for($i = 0; $i < 4; $i++)
            <tr><td colspan="9"></td></tr>
        @endfor
    </tfoot>
</table>
