<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Project;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Material;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TripController extends Controller
{
    // Trang chính: danh sách dự án có chuyến xe
    public function index()
    {
        $projects = Project::withCount('trips')
            ->withSum('trips', 'total_price')
            ->withSum('trips', 'volume_m3')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('trips.index', compact('projects'));
    }

    // Xem các tháng có chuyến xe trong 1 dự án
    public function byProject(Project $project)
    {
        $months = Trip::where('project_id', $project->id)
            ->select(
                DB::raw('YEAR(trip_date) as year'),
                DB::raw('MONTH(trip_date) as month'),
                DB::raw('COUNT(*) as trip_count'),
                DB::raw('SUM(volume_m3) as total_volume'),
                DB::raw('SUM(total_price) as total_price')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // 2. Dữ liệu biểu đồ xu hướng tháng cho dự án này
    $trendStats = Trip::where('project_id', $project->id)
        ->select(
            DB::raw("DATE_FORMAT(trip_date, '%Y-%m') as month"),
            DB::raw("SUM(total_price) as revenue"),
            DB::raw("SUM(volume_m3) as volume")
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

    $chartMonths = [];
    $chartRevenue = [];
    $chartVolume = [];
    foreach ($trendStats as $stat) {
        $chartMonths[] = Carbon::parse($stat->month . '-01')->format('m/Y');
        $chartRevenue[] = (int)$stat->revenue;
        $chartVolume[] = (float)$stat->volume;
    }

    // 3. Cơ cấu vật liệu trong dự án này
    $materialStats = Trip::where('project_id', $project->id)
        ->select('materials.name', DB::raw('SUM(total_price) as total_revenue'))
        ->join('materials', 'trips.material_id', '=', 'materials.id')
        ->groupBy('materials.id', 'materials.name')
        ->orderBy('total_revenue', 'desc')
        ->get();

    $materialNames = $materialStats->pluck('name');
    $materialRevenue = $materialStats->pluck('total_revenue')->map(fn($v) => (int)$v);

        // Tổng toàn dự án
        $projectSummary = [
            'total_trips' => $months->sum('trip_count'),
            'total_volume' => $months->sum('total_volume'),
            'total_price' => $months->sum('total_price'),
        ];

        return view('trips.project', compact(
            'project', 'months', 'projectSummary',
            'chartMonths', 'chartRevenue', 'chartVolume',
            'materialNames', 'materialRevenue'
        ));
    }

    // Xem chi tiết chuyến xe trong 1 tháng của 1 dự án
    public function byMonth(Project $project, int $year, int $month)
    {
        $trips = Trip::with(['vehicle', 'driver', 'material', 'route'])
            ->where('project_id', $project->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->orderBy('trip_date')
            ->orderBy('id')
            ->get();

        $summary = [
            'total_trips' => $trips->count(),
            'total_volume' => $trips->sum('volume_m3'),
            'total_price' => $trips->sum('total_price'),
        ];

        $monthLabel = "Tháng {$month}/{$year}";

        return view('trips.month', compact('project', 'trips', 'summary', 'year', 'month', 'monthLabel'));
    }

    public function create()
    {
        $projects = Project::active()->orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $employees = Employee::active()->orderBy('name')->get();
        $materials = Material::active()->orderBy('name')->get();
        $routes = Route::active()->get();

        return view('trips.create', compact('projects', 'vehicles', 'employees', 'materials', 'routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'volume_m3' => 'required|numeric|min:0',
            'price_per_m3' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['volume_m3'] * $validated['price_per_m3'];
        
        $material = Material::find($validated['material_id']);
        $validated['cost_per_m3'] = $material->import_price;
        $validated['profit'] = ($validated['price_per_m3'] - $material->import_price) * $validated['volume_m3'];

        Trip::create($validated);

        // Nếu nhấn "Lưu & Thêm mới"
        if ($request->has('save_and_new')) {
            return redirect()->route('trips.create', [
                'trip_date' => $validated['trip_date'],
                'project_id' => $validated['project_id'],
            ])->with('success', 'Đã lưu chuyến xe. Tiếp tục thêm mới.');
        }

        // Redirect về trang tháng tương ứng
        $date = \Carbon\Carbon::parse($validated['trip_date']);
        return redirect()->route('trips.by-month', [
            'project' => $validated['project_id'],
            'year' => $date->year,
            'month' => $date->month,
        ])->with('success', 'Đã thêm chuyến xe thành công.');
    }

    public function edit(Trip $trip)
    {
        $projects = Project::active()->orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $employees = Employee::active()->orderBy('name')->get();
        $materials = Material::active()->orderBy('name')->get();
        $routes = Route::active()->get();

        return view('trips.edit', compact('trip', 'projects', 'vehicles', 'employees', 'materials', 'routes'));
    }

    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'volume_m3' => 'required|numeric|min:0',
            'price_per_m3' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['volume_m3'] * $validated['price_per_m3'];
        
        $material = Material::find($validated['material_id']);
        $validated['cost_per_m3'] = $material->import_price;
        $validated['profit'] = ($validated['price_per_m3'] - $material->import_price) * $validated['volume_m3'];

        $trip->update($validated);

        // Redirect về trang tháng tương ứng
        $date = \Carbon\Carbon::parse($validated['trip_date']);
        return redirect()->route('trips.by-month', [
            'project' => $validated['project_id'],
            'year' => $date->year,
            'month' => $date->month,
        ])->with('success', 'Đã cập nhật chuyến xe.');
    }

    public function destroy(Trip $trip)
    {
        $projectId = $trip->project_id;
        $date = $trip->trip_date;
        $trip->delete();

        return redirect()->route('trips.by-month', [
            'project' => $projectId,
            'year' => $date->year,
            'month' => $date->month,
        ])->with('success', 'Đã xoá chuyến xe.');
    }

    // API endpoint: lấy volume mặc định của xe
    public function getVehicleVolume(Vehicle $vehicle)
    {
        return response()->json(['default_volume_m3' => $vehicle->default_volume_m3]);
    }

}
