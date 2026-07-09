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
        $projects = Project::withSum('trips', 'quantity')
            ->withSum('trips', 'total_price')
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

    // Lịch tháng: mỗi ngày = 1 card (số chuyến + tổng tiền + số phát sinh).
    public function byMonth(Project $project, int $year, int $month)
    {
        try {
            $firstOfMonth = Carbon::create($year, $month, 1);
        } catch (\Throwable) {
            abort(404);
        }
        $daysInMonth = $firstOfMonth->daysInMonth;
        // dayOfWeekIso: Mon=1..Sun=7 → dùng để chèn cell trống đầu tháng.
        $leadingBlanks = $firstOfMonth->dayOfWeekIso - 1;

        // Aggregate in PHP để tránh phụ thuộc DAY()/strftime() giữa MySQL và SQLite.
        $tripRows = Trip::where('project_id', $project->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->get(['trip_date', 'quantity', 'total_price']);

        $adjRows = \App\Models\SalaryAdjustment::where('project_id', $project->id)
            ->whereYear('trip_date', $year)
            ->whereMonth('trip_date', $month)
            ->get(['trip_date']);

        $tripsByDay = $tripRows->groupBy(fn ($t) => (int) $t->trip_date->day);
        $adjustmentsByDay = $adjRows->groupBy(fn ($a) => (int) $a->trip_date->day);

        $days = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayTrips = $tripsByDay->get($d, collect());
            $days[] = [
                'day' => $d,
                'trip_count' => (int) $dayTrips->sum('quantity'),
                'total_price' => (float) $dayTrips->sum('total_price'),
                'adj_count' => (int) $adjustmentsByDay->get($d, collect())->count(),
            ];
        }

        $summary = [
            'total_trips' => array_sum(array_column($days, 'trip_count')),
            'total_price' => array_sum(array_column($days, 'total_price')),
            'total_adj_count' => array_sum(array_column($days, 'adj_count')),
        ];

        $monthLabel = "Tháng {$month}/{$year}";

        return view('trips.month', compact(
            'project', 'days', 'leadingBlanks', 'daysInMonth',
            'summary', 'year', 'month', 'monthLabel'
        ));
    }

    // Chi tiết 1 ngày: danh sách chuyến + gom nhóm theo Xe (snapshot) + theo Vật liệu.
    public function byDay(Project $project, int $year, int $month, int $day)
    {
        try {
            $date = Carbon::create($year, $month, $day);
        } catch (\Throwable) {
            abort(404);
        }

        $trips = Trip::with(['material', 'route', 'driver', 'vehicle'])
            ->where('project_id', $project->id)
            ->whereDate('trip_date', $date)
            ->orderBy('id')
            ->get();

        $adjustments = \App\Models\SalaryAdjustment::with(['driver'])
            ->where('project_id', $project->id)
            ->whereDate('trip_date', $date)
            ->orderBy('id')
            ->get();

        // Gom theo snapshot biển số + tên tài xế (đúng lịch sử: đổi tài xế mặc định
        // sau này không thay đổi phân nhóm quá khứ).
        $byVehicle = $trips->groupBy(fn ($t) => ($t->vehicle_plate_snapshot ?? '—') . '|' . ($t->driver_name_snapshot ?? '—'))
            ->map(fn ($g) => [
                'plate' => $g->first()->vehicle_plate_snapshot ?? '—',
                'driver_name' => $g->first()->driver_name_snapshot ?? '—',
                'trip_count' => $g->sum('quantity'),
                'total_price' => $g->sum('total_price'),
            ])
            ->sortByDesc('trip_count')
            ->values();

        $byMaterial = $trips->groupBy('material_id')
            ->map(fn ($g) => [
                'material' => optional($g->first()->material)->name ?? '—',
                'trip_count' => $g->sum('quantity'),
                'total_price' => $g->sum('total_price'),
            ])
            ->sortByDesc('trip_count')
            ->values();

        $byRoute = $trips->groupBy('route_id')
            ->map(function ($g) {
                $route = $g->first()->route;
                $label = $route
                    ? ($route->from_location . ' → ' . $route->to_location)
                    : '—';

                return [
                    'route' => $label,
                    'trip_count' => $g->sum('quantity'),
                    'total_price' => $g->sum('total_price'),
                ];
            })
            ->sortByDesc('trip_count')
            ->values();

        $summary = [
            'total_trips' => $trips->sum('quantity'),
            'total_price' => $trips->sum('total_price'),
            'total_additions' => $adjustments->where('type', 'addition')->sum('amount'),
            'total_deductions' => $adjustments->where('type', 'deduction')->sum('amount'),
        ];

        $dayLabel = $date->format('d/m/Y');

        return view('trips.day', compact(
            'project', 'year', 'month', 'day', 'dayLabel',
            'trips', 'adjustments', 'byVehicle', 'byMaterial', 'byRoute', 'summary'
        ));
    }

    public function create(Request $request)
    {
        $projects = Project::active()->orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $employees = Employee::active()->orderBy('name')->get();
        $materials = Material::active()->orderBy('name')->get();
        $routes = Route::active()->get();

        $selectedRouteId = old('route_id', $request->get('route_id'));
        $selectedRoute = $selectedRouteId ? Route::find($selectedRouteId) : null;

        // --- Fetch trips and salary adjustments của ngày đang nhập cho bảng bên dưới ---
        $filterDate = $request->get('filter_date', $request->get('trip_date', date('Y-m-d')));
        $projectId = $request->get('project_id');

        $query = Trip::with(['project', 'vehicle', 'driver', 'material', 'route'])
            ->whereDate('trip_date', $filterDate);

        $adjQuery = \App\Models\SalaryAdjustment::with(['project', 'driver'])
            ->whereDate('trip_date', $filterDate);

        if ($projectId) {
            $query->where('project_id', $projectId);
            $adjQuery->where('project_id', $projectId);
        }

        $trips = $query->get();
        $adjustments = $adjQuery->get();

        foreach ($adjustments as $adj) {
            $adj->is_adjustment = true;
            $adj->total_price = $adj->type === 'addition' ? $adj->amount : -$adj->amount;
        }

        $recentTrips = $trips->concat($adjustments)->sortByDesc(function ($item) {
            return sprintf('%010d', $item->id);
        })->values();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('trips.partials.recent_trips_rows', compact('recentTrips'))->render(),
                'cards' => view('trips.partials.recent_trips_cards', compact('recentTrips'))->render(),
            ]);
        }

        return view('trips.create', compact(
            'projects', 'vehicles', 'employees', 'materials', 'routes', 'selectedRoute',
            'recentTrips', 'filterDate'
        ));
    }

    public function store(Request $request)
    {
        if ($request->has('freight_price')) $request->merge(['freight_price' => str_replace('.', '', $request->freight_price)]);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'quantity' => 'required|integer|min:1',
            'freight_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['freight_price'];
        $validated['driver_name_snapshot'] = Employee::find($validated['driver_id'])?->name;
        $validated['vehicle_plate_snapshot'] = Vehicle::find($validated['vehicle_id'])?->plate_number;

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

    // API: khi chọn xe trong form nhập chuyến, trả về tài xế mặc định của xe.
    public function getVehicleDefaultDriver(Vehicle $vehicle)
    {
        $driver = $vehicle->defaultDriver;

        return response()->json([
            'driver_id' => $driver?->id,
            'driver_name' => $driver?->name,
        ]);
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
        if ($request->has('freight_price')) $request->merge(['freight_price' => str_replace('.', '', $request->freight_price)]);

        $validated = $request->validate([
            'trip_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:employees,id',
            'material_id' => 'required|exists:materials,id',
            'route_id' => 'required|exists:routes,id',
            'quantity' => 'required|integer|min:1',
            'freight_price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['total_price'] = $validated['quantity'] * $validated['freight_price'];
        $validated['driver_name_snapshot'] = Employee::find($validated['driver_id'])?->name;
        $validated['vehicle_plate_snapshot'] = Vehicle::find($validated['vehicle_id'])?->plate_number;

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

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('trips.by-month', [
            'project' => $projectId,
            'year' => $date->year,
            'month' => $date->month,
        ])->with('success', 'Đã xoá chuyến xe.');
    }

}
