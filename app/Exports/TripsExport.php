<?php

namespace App\Exports;

use App\Models\Trip;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TripsExport implements FromView, WithTitle, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
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

        $trips = $query->orderBy('trip_date')->orderBy('id')->get();

        return view('exports.trips', [
            'trips' => $trips,
            'filters' => $this->filters
        ]);
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
