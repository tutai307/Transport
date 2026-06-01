<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Trip;
use App\Exports\TripsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    public function index()
    {
        $employees = Employee::withSum('trips', 'quantity')
            ->withSum('trips', 'total_price')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        foreach ($employees as $employee) {
            $totalAdditions = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
                ->where('type', 'addition')
                ->sum('amount');
            $totalDeductions = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
                ->where('type', 'deduction')
                ->sum('amount');
            $employee->net_salary = ($employee->trips_sum_total_price ?? 0) + $totalAdditions - $totalDeductions;
        }

        return view('payroll.index', compact('employees'));
    }

    public function byDriver(Employee $employee)
    {
        // Lấy tất cả năm có chuyến xe
        $tripYears = Trip::where('driver_id', $employee->id)
            ->pluck('trip_date')
            ->map(function($date) {
                return $date ? \Carbon\Carbon::parse($date)->year : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        // Lấy tất cả năm có phát sinh lương
        $adjYears = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
            ->pluck('trip_date')
            ->map(function($date) {
                return $date ? \Carbon\Carbon::parse($date)->year : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        $allYears = array_unique(array_merge($tripYears, $adjYears));
        rsort($allYears);

        $years = [];
        $summary = [
            'trip_count'   => 0,
            'total_salary' => 0,
            'total_additions' => 0,
            'total_deductions' => 0,
            'net_salary' => 0,
        ];

        foreach ($allYears as $yr) {
            $tripsInYear = Trip::where('driver_id', $employee->id)
                ->whereYear('trip_date', $yr)
                ->get();

            $adjustmentsInYear = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
                ->whereYear('trip_date', $yr)
                ->get();

            $tripCount = $tripsInYear->sum('quantity');
            $totalSalary = $tripsInYear->sum('total_price');
            $totalAdditions = $adjustmentsInYear->where('type', 'addition')->sum('amount');
            $totalDeductions = $adjustmentsInYear->where('type', 'deduction')->sum('amount');
            $netSalary = $totalSalary + $totalAdditions - $totalDeductions;

            $years[] = (object) [
                'year' => $yr,
                'trip_count' => $tripCount,
                'total_salary' => $totalSalary,
                'total_additions' => $totalAdditions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
            ];

            $summary['trip_count'] += $tripCount;
            $summary['total_salary'] += $totalSalary;
            $summary['total_additions'] += $totalAdditions;
            $summary['total_deductions'] += $totalDeductions;
            $summary['net_salary'] += $netSalary;
        }

        $years = collect($years);

        return view('payroll.driver', compact('employee', 'years', 'summary'));
    }

    public function byYear(Employee $employee, $year)
    {
        $year = (int) $year;
        // Lấy tất cả tháng có chuyến xe
        $tripMonths = Trip::where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->pluck('trip_date')
            ->map(function($date) {
                return $date ? \Carbon\Carbon::parse($date)->month : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        // Lấy tất cả tháng có phát sinh lương
        $adjMonths = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->pluck('trip_date')
            ->map(function($date) {
                return $date ? \Carbon\Carbon::parse($date)->month : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        $allMonths = array_unique(array_merge($tripMonths, $adjMonths));
        sort($allMonths);

        $months = [];
        $yearTotal = [
            'trip_count'   => 0,
            'total_salary' => 0,
            'total_additions' => 0,
            'total_deductions' => 0,
            'net_salary' => 0,
        ];

        foreach ($allMonths as $m) {
            $tripsInMonth = Trip::where('driver_id', $employee->id)
                ->whereYear('trip_date', $year)
                ->whereMonth('trip_date', $m)
                ->get();

            $adjustmentsInMonth = \App\Models\SalaryAdjustment::where('driver_id', $employee->id)
                ->whereYear('trip_date', $year)
                ->whereMonth('trip_date', $m)
                ->get();

            $tripCount = $tripsInMonth->sum('quantity');
            $totalSalary = $tripsInMonth->sum('total_price');
            $totalAdditions = $adjustmentsInMonth->where('type', 'addition')->sum('amount');
            $totalDeductions = $adjustmentsInMonth->where('type', 'deduction')->sum('amount');
            $netSalary = $totalSalary + $totalAdditions - $totalDeductions;

            $months[] = (object) [
                'month' => $m,
                'trip_count' => $tripCount,
                'total_salary' => $totalSalary,
                'total_additions' => $totalAdditions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
            ];

            $yearTotal['trip_count'] += $tripCount;
            $yearTotal['total_salary'] += $totalSalary;
            $yearTotal['total_additions'] += $totalAdditions;
            $yearTotal['total_deductions'] += $totalDeductions;
            $yearTotal['net_salary'] += $netSalary;
        }

        $months = collect($months);

        return view('payroll.year', compact('employee', 'year', 'months', 'yearTotal'));
    }

    public function export(Request $request, Employee $employee)
    {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $filters = [
            'driver_id'    => $employee->id,
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            'export_type'  => 'freight',
            'hide_project' => true,
        ];

        $fileName = 'phieu-luong_'
            . \Str::slug($employee->name) . '_'
            . $dateFrom . '_to_' . $dateTo
            . '.xlsx';

        return Excel::download(new TripsExport($filters), $fileName);
    }

    public function byMonth(Employee $employee, $year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;
        $trips = Trip::with(['vehicle', 'project', 'material', 'route'])
            ->where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->orderBy('trip_date')
            ->get();

        $adjustments = \App\Models\SalaryAdjustment::with(['project'])
            ->where('driver_id', $employee->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->orderBy('trip_date')
            ->get();

        $monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5',
                       'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $monthLabel = $monthNames[$month] . '/' . $year;

        $totalTripsSalary = $trips->sum('total_price');
        $totalAdditions = $adjustments->where('type', 'addition')->sum('amount');
        $totalDeductions = $adjustments->where('type', 'deduction')->sum('amount');

        $summary = [
            'trip_count'       => $trips->sum('quantity'),
            'total_salary'     => $totalTripsSalary,
            'total_additions'  => $totalAdditions,
            'total_deductions' => $totalDeductions,
            'net_salary'       => $totalTripsSalary + $totalAdditions - $totalDeductions,
        ];

        return view('payroll.month', compact('employee', 'year', 'month', 'monthLabel', 'trips', 'adjustments', 'summary'));
    }
}
