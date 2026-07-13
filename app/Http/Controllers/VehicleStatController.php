<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;

class VehicleStatController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::withCount('trips')
            ->withSum('trips', 'quantity')
            ->withSum('trips', 'total_price')
            ->orderBy('is_active', 'desc')
            ->orderBy('plate_number')
            ->get();

        return view('vehicle-stats.index', compact('vehicles'));
    }

    public function byVehicle(Vehicle $vehicle)
    {
        // Lấy tất cả năm có chuyến xe
        $tripYears = Trip::where('vehicle_id', $vehicle->id)
            ->pluck('trip_date')
            ->map(function ($date) {
                return $date ? \Carbon\Carbon::parse($date)->year : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        rsort($tripYears);

        $years = [];
        $summary = ['trip_count' => 0, 'total_amount' => 0];

        foreach ($tripYears as $yr) {
            $tripsInYear = Trip::where('vehicle_id', $vehicle->id)
                ->whereYear('trip_date', $yr)
                ->get();

            $tripCount = $tripsInYear->sum('quantity');
            $totalAmount = $tripsInYear->sum('total_price');

            $years[] = (object) [
                'year' => $yr,
                'trip_count' => $tripCount,
                'total_amount' => $totalAmount,
            ];

            $summary['trip_count'] += $tripCount;
            $summary['total_amount'] += $totalAmount;
        }

        $years = collect($years);

        return view('vehicle-stats.vehicle', compact('vehicle', 'years', 'summary'));
    }

    public function byYear(Vehicle $vehicle, $year)
    {
        $year = (int) $year;

        $tripMonths = Trip::where('vehicle_id', $vehicle->id)
            ->whereYear('trip_date', $year)
            ->pluck('trip_date')
            ->map(function ($date) {
                return $date ? \Carbon\Carbon::parse($date)->month : null;
            })
            ->filter()
            ->unique()
            ->toArray();

        sort($tripMonths);

        $months = [];
        $yearTotal = ['trip_count' => 0, 'total_amount' => 0];

        foreach ($tripMonths as $m) {
            $tripsInMonth = Trip::where('vehicle_id', $vehicle->id)
                ->whereYear('trip_date', $year)
                ->whereMonth('trip_date', $m)
                ->get();

            $tripCount = $tripsInMonth->sum('quantity');
            $totalAmount = $tripsInMonth->sum('total_price');

            $months[] = (object) [
                'month' => $m,
                'trip_count' => $tripCount,
                'total_amount' => $totalAmount,
            ];

            $yearTotal['trip_count'] += $tripCount;
            $yearTotal['total_amount'] += $totalAmount;
        }

        $months = collect($months);

        return view('vehicle-stats.year', compact('vehicle', 'year', 'months', 'yearTotal'));
    }

    public function byMonth(Vehicle $vehicle, $year, $month)
    {
        $year = (int) $year;
        $month = (int) $month;

        $trips = Trip::with(['driver', 'project', 'material', 'route'])
            ->where('vehicle_id', $vehicle->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->orderBy('trip_date')
            ->get();

        $monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5',
                       'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $monthLabel = $monthNames[$month] . '/' . $year;

        $summary = [
            'trip_count' => $trips->sum('quantity'),
            'total_amount' => $trips->sum('total_price'),
        ];

        return view('vehicle-stats.month', compact('vehicle', 'year', 'month', 'monthLabel', 'trips', 'summary'));
    }
}
