<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Chỉ số tổng quan
        $totalTrips = Trip::count();
        $totalRevenue = Trip::sum('total_price');
        $totalProfit = Trip::sum('profit');

        // 2. Dữ liệu biểu đồ xu hướng (6 tháng gần nhất)
        $monthlyStats = Trip::select(
            DB::raw("DATE_FORMAT(trip_date, '%Y-%m') as month"),
            DB::raw("SUM(total_price) as revenue"),
            DB::raw("SUM(profit) as profit")
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->limit(6)
        ->get();

        $chartMonths = [];
        $chartRevenue = [];
        $chartProfit = [];

        foreach ($monthlyStats as $stat) {
            $chartMonths[] = Carbon::parse($stat->month . '-01')->format('m/Y');
            $chartRevenue[] = (int)$stat->revenue;
            $chartProfit[] = (int)$stat->profit;
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
            $projectRevenue[] = (int)$stat->total_revenue;
        }

        return view('dashboard', compact(
            'totalTrips', 'totalRevenue', 'totalProfit',
            'chartMonths', 'chartRevenue', 'chartProfit',
            'projectNames', 'projectRevenue'
        ));
    }
}
