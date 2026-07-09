<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Chỉ số tổng quan
        $totalTrips = Trip::sum('quantity');
        $totalFreightAmount = Trip::sum('total_price');

        // 2. Biểu đồ tiền cước 6 tháng gần nhất
        $monthlyStats = Trip::select(
            DB::raw("DATE_FORMAT(trip_date, '%Y-%m') as month"),
            DB::raw("SUM(total_price) as freight"),
            DB::raw("SUM(quantity) as trips")
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->limit(6)
        ->get();

        $chartMonths = [];
        $chartFreight = [];
        $chartTrips = [];

        foreach ($monthlyStats as $stat) {
            $chartMonths[] = Carbon::parse($stat->month . '-01')->format('m/Y');
            $chartFreight[] = (int) $stat->freight;
            $chartTrips[] = (int) $stat->trips;
        }

        // 3. Số chuyến xe theo dự án (Top 5)
        $projectStats = Trip::select('projects.name', DB::raw('SUM(quantity) as total_trips'))
            ->join('projects', 'trips.project_id', '=', 'projects.id')
            ->groupBy('projects.id', 'projects.name')
            ->orderBy('total_trips', 'desc')
            ->limit(5)
            ->get();

        $projectNames = [];
        $projectTrips = [];
        foreach ($projectStats as $stat) {
            $projectNames[] = $stat->name;
            $projectTrips[] = (int) $stat->total_trips;
        }

        // 4. Cảnh báo: chuyến xe chưa gán tài xế
        $tripsWithoutDriverCount = Trip::whereNull('driver_id')->count();
        $tripsWithoutDriver = Trip::with(['project', 'vehicle'])
            ->whereNull('driver_id')
            ->orderByDesc('trip_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalTrips', 'totalFreightAmount',
            'chartMonths', 'chartFreight', 'chartTrips',
            'projectNames', 'projectTrips',
            'tripsWithoutDriverCount', 'tripsWithoutDriver'
        ));
    }
}
