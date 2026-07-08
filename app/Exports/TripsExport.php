<?php

namespace App\Exports;

use App\Models\Trip;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TripsExport implements FromView, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = Trip::with(['project', 'material', 'route']);
        $adjQuery = \App\Models\SalaryAdjustment::with(['project', 'driver']);

        if (! empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
            $adjQuery->where('project_id', $this->filters['project_id']);
        }

        if (! empty($this->filters['year']) && ! empty($this->filters['month'])) {
            $query->whereYear('trip_date', $this->filters['year'])
                ->whereMonth('trip_date', $this->filters['month']);
            $adjQuery->whereYear('trip_date', $this->filters['year'])
                ->whereMonth('trip_date', $this->filters['month']);
        } elseif (! empty($this->filters['date_from']) || ! empty($this->filters['date_to'])) {
            $dateFrom = $this->filters['date_from'] ?? now()->startOfMonth()->format('Y-m-d');
            $dateTo = $this->filters['date_to'] ?? now()->format('Y-m-d');
            $query->whereBetween('trip_date', [$dateFrom, $dateTo]);
            $adjQuery->whereBetween('trip_date', [$dateFrom, $dateTo]);
        }

        if (! empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
            // Phát sinh lương không gắn với xe → bỏ hoàn toàn khi lọc theo xe.
            $adjQuery->whereRaw('1=0');
        }

        if (! empty($this->filters['driver_id'])) {
            $query->where('driver_id', $this->filters['driver_id']);
            $adjQuery->where('driver_id', $this->filters['driver_id']);
        }

        $trips = $query->orderBy('trip_date')->orderBy('id')->get();
        $adjustments = $adjQuery->orderBy('trip_date')->orderBy('id')->get();

        foreach ($adjustments as $adj) {
            $adj->is_adjustment = true;
            $adj->total_price = $adj->type === 'addition' ? $adj->amount : -$adj->amount;
        }

        $allRecords = $trips->concat($adjustments)->sortBy(function ($item) {
            $datePrefix = $item->trip_date->format('Ymd');
            $idSuffix = sprintf('%010d', $item->id);
            return $datePrefix . $idSuffix;
        })->values();

        return view('exports.trips', [
            'trips' => $trips,
            'allRecords' => $allRecords,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        $title = 'Báo cáo chuyến xe';

        if (! empty($this->filters['project_id'])) {
            $project = Project::find($this->filters['project_id']);
            if ($project) {
                $title .= ' - ' . $project->name;
            }
        }

        if (! empty($this->filters['year']) && ! empty($this->filters['month'])) {
            $title .= ' - T' . $this->filters['month'] . '/' . $this->filters['year'];
        }

        return $title;
    }

    public function columnFormats(): array
    {
        $showProject = empty($this->filters['project_id']) && empty($this->filters['hide_project']);

        // 2 cột tiền: đơn giá cước, thành tiền.
        return $showProject
            ? ['H' => '#,##0', 'I' => '#,##0']
            : ['G' => '#,##0', 'H' => '#,##0'];
    }
}
