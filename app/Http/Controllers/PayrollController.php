<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $employees = Employee::withCount('trips')
            ->withSum('trips', 'total_price')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('payroll.index', compact('employees'));
    }

    public function byDriver(Employee $employee)
    {
        $years = Trip::where('driver_id', $employee->id)
            ->select(
                DB::raw('YEAR(trip_date) as year'),
                DB::raw('COUNT(*) as trip_count'),
                DB::raw('SUM(total_price) as total_salary')
            )
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        $summary = [
            'trip_count'   => $years->sum('trip_count'),
            'total_salary' => $years->sum('total_salary'),
        ];

        return view('payroll.driver', compact('employee', 'years', 'summary'));
    }

    public function byYear(Employee $employee, int $year)
    {
        $months = Trip::where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->select(
                DB::raw('MONTH(trip_date) as month'),
                DB::raw('COUNT(*) as trip_count'),
                DB::raw('SUM(total_price) as total_salary')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $yearTotal = [
            'trip_count'   => $months->sum('trip_count'),
            'total_salary' => $months->sum('total_salary'),
        ];

        return view('payroll.year', compact('employee', 'year', 'months', 'yearTotal'));
    }

    public function byMonth(Employee $employee, int $year, int $month)
    {
        $trips = Trip::with(['vehicle', 'project', 'material', 'route'])
            ->where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->orderBy('trip_date')
            ->get();

        $monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5',
                       'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $monthLabel = $monthNames[$month] . '/' . $year;

        $summary = [
            'trip_count'   => $trips->count(),
            'total_salary' => $trips->sum('total_price'),
        ];

        return view('payroll.month', compact('employee', 'year', 'month', 'monthLabel', 'trips', 'summary'));
    }
}
