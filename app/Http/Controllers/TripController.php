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
        $projects = Project::select('projects.*')
            ->leftJoin('trips', 'projects.id', '=', 'trips.project_id')
            ->selectRaw("SUM(trips.quantity) as total_trips_count")
            ->selectRaw("COUNT(trips.id) as record_count")
            ->withSum('trips', 'total_price')
            ->groupBy('projects.id')
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
                DB::raw("SUM(quantity) as trip_count"),
                DB::raw('SUM(total_price) as total_price')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

            // 2. Dữ liệu biểu đồ theo ngày (Số chuyến)
        $dailyStats = Trip::where('project_id', $project->id)
            ->select(
                'trip_date',
                DB::raw("SUM(quantity) as trip_count")
            )
            ->groupBy('trip_date')
            ->orderBy('trip_date', 'asc')
            ->get();

        // Tổng toàn dự án
        $projectSummary = [
            'total_trips' => $months->sum('trip_count'),
            'total_price' => $months->sum('total_price'),
        ];
        
        $chartDates = [];
        $chartTripCount = [];
        foreach ($dailyStats as $stat) {
            $chartDates[] = $stat->trip_date->format('d/m');
            $chartTripCount[] = (float)$stat->trip_count;
        }

        return view('trips.project', compact(
            'project', 'months', 'projectSummary',
            'chartDates', 'chartTripCount'
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
            'total_trips' => $trips->sum('quantity'),
            'total_price' => $trips->sum('total_price'),
        ];

        $monthLabel = "Tháng {$month}/{$year}";

        return view('trips.month', compact('project', 'trips', 'summary', 'year', 'month', 'monthLabel'));
    }

    public function create(Request $request)
    {
        $projects = Project::active()->orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $employees = Employee::active()->orderBy('name')->get();
        $materials = Material::active()->orderBy('name')->get();
        $routes = Route::active()->get();

        // --- Fetch recent trips for the table below ---
        $filterMonth = $request->get('filter_month', date('n'));
        $filterYear = $request->get('filter_year', date('Y'));
        $projectId = $request->get('project_id');

        $query = Trip::with(['project', 'vehicle', 'driver', 'material', 'route'])
            ->whereYear('trip_date', $filterYear)
            ->whereMonth('trip_date', $filterMonth);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $recentTrips = $query->orderBy('trip_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('trips.partials.recent_trips_rows', compact('recentTrips'))->render(),
                'cards' => view('trips.partials.recent_trips_cards', compact('recentTrips'))->render(),
            ]);
        }

        return view('trips.create', compact(
            'projects', 'vehicles', 'employees', 'materials', 'routes',
            'recentTrips', 'filterMonth', 'filterYear'
        ));
    }

    public function store(Request $request)
    {
        // Loại bỏ dấu chấm phân cách hàng nghìn khỏi các trường giá tiền
        if ($request->has('freight_price')) $request->merge(['freight_price' => str_replace('.', '', $request->freight_price)]);
        if ($request->has('sell_price')) $request->merge(['sell_price' => str_replace('.', '', $request->sell_price)]);
        if ($request->has('buy_price')) $request->merge(['buy_price' => str_replace('.', '', $request->buy_price)]);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'quantity' => 'required|numeric|min:0',
            'freight_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['freight_price'];
        $validated['profit'] = ($validated['sell_price'] - $validated['buy_price']) * $validated['quantity'];

        Trip::create($validated);

        // Nếu nhấn "Lưu & Thêm mới"
        if ($request->has('save_and_new')) {
            return redirect()->route('trips.create', [
                'trip_date' => $validated['trip_date'],
                'project_id' => $validated['project_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'driver_id' => $validated['driver_id'],
                'material_id' => $validated['material_id'],
                'route_id' => $validated['route_id'],
                'freight_price' => $validated['freight_price'],
                'buy_price' => $validated['buy_price'],
                'sell_price' => $validated['sell_price'],
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
        // Loại bỏ dấu chấm phân cách hàng nghìn khỏi các trường giá tiền
        if ($request->has('freight_price')) $request->merge(['freight_price' => str_replace('.', '', $request->freight_price)]);
        if ($request->has('sell_price')) $request->merge(['sell_price' => str_replace('.', '', $request->sell_price)]);
        if ($request->has('buy_price')) $request->merge(['buy_price' => str_replace('.', '', $request->buy_price)]);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'quantity' => 'required|numeric|min:0',
            'freight_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['freight_price'];
        $validated['profit'] = ($validated['sell_price'] - $validated['buy_price']) * $validated['quantity'];

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

}
