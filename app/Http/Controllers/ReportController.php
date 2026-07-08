<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Employee;
use App\Exports\TripsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $query = Trip::with(['project', 'vehicle', 'driver', 'material', 'route']);

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->format('Y-m-d'));
        $query->whereBetween('trip_date', [$dateFrom, $dateTo]);

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $trips = $query->orderBy('trip_date')->orderBy('id')->get();

        $summary = [
            'total_trips'  => $trips->sum('quantity'),
            'total_price'  => $trips->sum('total_price'),
        ];

        return view('reports.index', compact('trips', 'employees', 'summary', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->format('Y-m-d'));

        $parts = ['hoa-don-cuoc-phi', $dateFrom . '_' . $dateTo];

        if ($request->filled('driver_id')) {
            $driver = Employee::find($request->driver_id);
            if ($driver) {
                $parts[] = \Str::slug($driver->name);
            }
        }

        $fileName = implode('_', $parts) . '.xlsx';

        return Excel::download(new TripsExport($filters), $fileName);
    }
}
