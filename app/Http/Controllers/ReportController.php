<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Project;
use App\Models\Vehicle;
use App\Exports\TripsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('plate_number')->get();

        $query = Trip::with(['project', 'vehicle', 'driver', 'material', 'route']);

        // Lọc theo dự án
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Lọc theo tháng/năm
        if ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('trip_date', $request->year)
                  ->whereMonth('trip_date', $request->month);
        } else {
            // Mặc định: lọc theo khoảng ngày
            $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', now()->format('Y-m-d'));
            $query->whereBetween('trip_date', [$dateFrom, $dateTo]);
        }

        // Lọc theo xe
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $trips = $query->orderBy('trip_date')->orderBy('id')->get();

        // Tổng kết
        $summary = [
            'total_trips' => $trips->count(),
            'total_volume' => $trips->sum('volume_m3'),
            'total_price' => $trips->sum('total_price'),
            'total_profit' => $trips->sum('profit'),
        ];

        // Chi tiết lợi nhuận theo dự án (nếu không filter theo dự án lẻ)
        $profitByProject = [];
        if (!$request->filled('project_id')) {
            $profitByProject = $trips->groupBy('project_id')->map(function ($group) {
                return [
                    'project_name' => $group->first()->project->name,
                    'trip_count' => $group->count(),
                    'total_profit' => $group->sum('profit')
                ];
            })->sortByDesc('total_profit');
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        return view('reports.index', compact('trips', 'projects', 'vehicles', 'summary', 'profitByProject', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        // Tạo tên file mô tả
        $parts = ['bao-cao-chuyen-xe'];

        if ($request->filled('project_id')) {
            $project = Project::find($request->project_id);
            if ($project) {
                $parts[] = \Str::slug($project->name);
            }
        }

        if ($request->filled('year') && $request->filled('month')) {
            $parts[] = sprintf('thang-%02d-%d', $request->month, $request->year);
        } else {
            $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', now()->format('Y-m-d'));
            $parts[] = $dateFrom . '_' . $dateTo;
        }

        $fileName = implode('_', $parts) . '.xlsx';

        return Excel::download(
            new TripsExport($filters),
            $fileName
        );
    }
}
