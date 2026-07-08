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

        // 2. Biểu đồ doanh thu 6 tháng gần nhất
        $monthlyStats = Trip::select(
            DB::raw("DATE_FORMAT(trip_date, '%Y-%m') as month"),
            DB::raw("SUM(total_price) as revenue")
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->limit(6)
        ->get();

        $chartMonths = [];
        $chartRevenue = [];

        foreach ($monthlyStats as $stat) {
            $chartMonths[] = Carbon::parse($stat->month . '-01')->format('m/Y');
            $chartRevenue[] = (int) $stat->revenue;
        }

        // 3. Cơ cấu doanh thu theo dự án (Top 5)
        $projectStats = Trip::select('projects.name', DB::raw('SUM(total_price) as total_revenue'))
            ->join('projects', 'trips.project_id', '=', 'projects.id')
            ->groupBy('projects.id', 'projects.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        $projectNames = [];
        $projectRevenue = [];
        foreach ($projectStats as $stat) {
            $projectNames[] = $stat->name;
            $projectRevenue[] = (int) $stat->total_revenue;
        }

        return view('dashboard', compact(
            'totalTrips', 'totalFreightAmount',
            'chartMonths', 'chartRevenue',
            'projectNames', 'projectRevenue'
        ));
    }
}
