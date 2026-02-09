<?php

namespace App\Exports;

use App\Models\Trip;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TripsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;
    protected int $rowNumber = 0;
    protected bool $singleProject = false;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->singleProject = !empty($filters['project_id']);
    }

    public function collection()
    {
        $query = Trip::with(['project', 'vehicle', 'driver', 'material', 'route']);

        // Lọc theo dự án
        if (!empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        // Lọc theo tháng/năm
        if (!empty($this->filters['year']) && !empty($this->filters['month'])) {
            $query->whereYear('trip_date', $this->filters['year'])
                  ->whereMonth('trip_date', $this->filters['month']);
        } elseif (!empty($this->filters['date_from']) || !empty($this->filters['date_to'])) {
            $dateFrom = $this->filters['date_from'] ?? now()->startOfMonth()->format('Y-m-d');
            $dateTo = $this->filters['date_to'] ?? now()->format('Y-m-d');
            $query->whereBetween('trip_date', [$dateFrom, $dateTo]);
        }

        // Lọc theo xe
        if (!empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        return $query->orderBy('trip_date')->orderBy('id')->get();
    }

    public function headings(): array
    {
        $headings = ['STT', 'Ngày'];

        // Nếu không lọc theo 1 dự án, hiển thị cột dự án
        if (!$this->singleProject) {
            $headings[] = 'Dự án';
        }

        return array_merge($headings, [
            'Xe',
            'Tài xế',
            'Vật liệu',
            'Tuyến đường',
            'KL (m³)',
            'Đơn giá/m³',
            'Thành tiền',
            'Ghi chú',
        ]);
    }

    public function map($trip): array
    {
        $this->rowNumber++;

        $row = [
            $this->rowNumber,
            $trip->trip_date->format('d/m/Y'),
        ];

        if (!$this->singleProject) {
            $row[] = $trip->project->name;
        }

        return array_merge($row, [
            $trip->vehicle->plate_number,
            $trip->driver->name,
            $trip->material->name,
            $trip->route->from_location . ' → ' . $trip->route->to_location,
            $trip->volume_m3,
            $trip->price_per_m3,
            $trip->total_price,
            $trip->note ?? '',
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $this->singleProject ? 'I' : 'J';
        $volCol = $this->singleProject ? 'G' : 'H';
        $priceCol = $this->singleProject ? 'H' : 'I';
        $totalCol = $this->singleProject ? 'I' : 'J';

        // Header row
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Borders
        $lastRow = $this->rowNumber + 1;
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Format số
        $sheet->getStyle("{$volCol}2:{$volCol}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("{$priceCol}2:{$totalCol}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    public function title(): string
    {
        $title = 'Báo cáo chuyến xe';

        if (!empty($this->filters['project_id'])) {
            $project = Project::find($this->filters['project_id']);
            if ($project) {
                $title .= ' - ' . $project->name;
            }
        }

        if (!empty($this->filters['year']) && !empty($this->filters['month'])) {
            $title .= ' - T' . $this->filters['month'] . '/' . $this->filters['year'];
        }

        return $title;
    }
}
