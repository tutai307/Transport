<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMark;
use App\Models\Material;
use App\Models\Project;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date      = $request->get('date', today()->format('Y-m-d'));
        $projectId = $request->integer('project_id') ?: null;

        $projects  = Project::active()->orderBy('name')->get();
        $vehicles  = Vehicle::active()->with('defaultDriver')->orderBy('plate_number')->get();
        $materials = Material::active()->orderBy('name')->get();
        $routes    = Route::active()->orderBy('from_location')->get();

        $marks         = [];  // [vehicleId_materialId => count]
        $vehicleConfigs = []; // [vehicleId => ['freight_price' => x, 'route_id' => y]]

        if ($projectId) {
            $existing = AttendanceMark::where('attendance_date', $date)
                ->where('project_id', $projectId)
                ->where('is_committed', false)
                ->get();

            foreach ($existing as $m) {
                $key           = $m->vehicle_id . '_' . $m->material_id;
                $marks[$key]   = ($marks[$key] ?? 0) + 1;

                // Latest config wins per vehicle
                $vehicleConfigs[$m->vehicle_id] = [
                    'freight_price' => (float) $m->freight_price,
                    'route_id'      => $m->route_id,
                ];
            }
        }

        return view('attendance.index', compact(
            'date', 'projects', 'projectId',
            'vehicles', 'materials', 'routes',
            'marks', 'vehicleConfigs'
        ));
    }

    public function mark(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => 'required|date',
            'project_id'      => 'required|exists:projects,id',
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_id'       => 'required|exists:employees,id',
            'material_id'     => 'required|exists:materials,id',
            'route_id'        => 'nullable|exists:routes,id',
            'freight_price'   => 'required|numeric|min:0',
        ]);

        AttendanceMark::create(array_merge($data, [
            'marked_at'    => now(),
            'is_committed' => false,
        ]));

        return response()->json([
            'count' => $this->countMarks(
                $data['attendance_date'], $data['project_id'],
                $data['vehicle_id'], $data['material_id']
            ),
        ]);
    }

    public function unmark(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => 'required|date',
            'project_id'      => 'required|exists:projects,id',
            'vehicle_id'      => 'required|exists:vehicles,id',
            'material_id'     => 'required|exists:materials,id',
        ]);

        AttendanceMark::where('attendance_date', $data['attendance_date'])
            ->where('project_id', $data['project_id'])
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('material_id', $data['material_id'])
            ->where('is_committed', false)
            ->orderBy('marked_at', 'desc')
            ->first()?->delete();

        return response()->json([
            'count' => $this->countMarks(
                $data['attendance_date'], $data['project_id'],
                $data['vehicle_id'], $data['material_id']
            ),
        ]);
    }

    public function updateConfig(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => 'required|date',
            'project_id'      => 'required|exists:projects,id',
            'vehicle_id'      => 'required|exists:vehicles,id',
            'route_id'        => 'nullable|exists:routes,id',
            'freight_price'   => 'required|numeric|min:0',
        ]);

        AttendanceMark::where('attendance_date', $data['attendance_date'])
            ->where('project_id', $data['project_id'])
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('is_committed', false)
            ->update([
                'route_id'      => $data['route_id'],
                'freight_price' => $data['freight_price'],
            ]);

        return response()->json(['ok' => true]);
    }

    public function details(Request $request)
    {
        $marks = AttendanceMark::where('attendance_date', $request->date)
            ->where('project_id', $request->project_id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where('is_committed', false)
            ->with(['material', 'route'])
            ->orderBy('marked_at')
            ->get();

        return response()->json($marks->map(fn($m) => [
            'material'      => $m->material->name,
            'route'         => $m->route?->full_name ?? '—',
            'freight_price' => number_format($m->freight_price, 0, ',', '.') . ' đ',
            'marked_at'     => $m->marked_at->format('H:i:s'),
        ]));
    }

    public function pending(Request $request)
    {
        $marks = AttendanceMark::where('attendance_date', $request->date)
            ->where('project_id', $request->project_id)
            ->where('is_committed', false)
            ->with(['vehicle', 'driver', 'material', 'route'])
            ->get();

        $grouped = [];
        foreach ($marks as $m) {
            $k = "{$m->vehicle_id}_{$m->material_id}_{$m->route_id}_{$m->freight_price}";
            if (!isset($grouped[$k])) {
                $grouped[$k] = [
                    'vehicle'       => $m->vehicle->plate_number,
                    'driver'        => $m->driver->name,
                    'material'      => $m->material->name,
                    'route'         => $m->route?->full_name ?? '—',
                    'freight_price' => (float) $m->freight_price,
                    'quantity'      => 0,
                ];
            }
            $grouped[$k]['quantity']++;
        }

        $rows = array_values($grouped);
        foreach ($rows as &$row) {
            $row['total'] = $row['quantity'] * $row['freight_price'];
        }

        return response()->json([
            'rows'         => $rows,
            'total_trips'  => $marks->count(),
            'total_amount' => $marks->sum('freight_price'),
        ]);
    }

    public function commit(Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'project_id' => 'required|exists:projects,id',
        ]);

        DB::beginTransaction();
        try {
            $marks = AttendanceMark::where('attendance_date', $data['date'])
                ->where('project_id', $data['project_id'])
                ->where('is_committed', false)
                ->with(['vehicle', 'driver'])
                ->get();

            if ($marks->isEmpty()) {
                return response()->json(['message' => 'Không có chuyến nào để nhập.'], 422);
            }

            $grouped = [];
            foreach ($marks as $m) {
                $k = "{$m->vehicle_id}_{$m->material_id}_{$m->route_id}_{$m->freight_price}";
                if (!isset($grouped[$k])) {
                    $grouped[$k] = [
                        'vehicle'       => $m->vehicle,
                        'driver'        => $m->driver,
                        'material_id'   => $m->material_id,
                        'route_id'      => $m->route_id,
                        'freight_price' => (float) $m->freight_price,
                        'quantity'      => 0,
                        'ids'           => [],
                    ];
                }
                $grouped[$k]['quantity']++;
                $grouped[$k]['ids'][] = $m->id;
            }

            foreach ($grouped as $info) {
                $trip = Trip::create([
                    'trip_date'              => $data['date'],
                    'project_id'             => $data['project_id'],
                    'vehicle_id'             => $info['vehicle']->id,
                    'vehicle_plate_snapshot' => $info['vehicle']->plate_number,
                    'driver_id'              => $info['driver']->id,
                    'driver_name_snapshot'   => $info['driver']->name,
                    'material_id'            => $info['material_id'],
                    'route_id'               => $info['route_id'],
                    'quantity'               => $info['quantity'],
                    'freight_price'          => $info['freight_price'],
                    'total_price'            => $info['quantity'] * $info['freight_price'],
                ]);

                AttendanceMark::whereIn('id', $info['ids'])->update([
                    'is_committed' => true,
                    'trip_id'      => $trip->id,
                ]);
            }

            DB::commit();
            return response()->json([
                'message'    => 'Đã nhập ' . count($grouped) . ' dòng chuyến xe thành công!',
                'trip_count' => count($grouped),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    private function countMarks(string $date, int $projectId, int $vehicleId, int $materialId): int
    {
        return AttendanceMark::where('attendance_date', $date)
            ->where('project_id', $projectId)
            ->where('vehicle_id', $vehicleId)
            ->where('material_id', $materialId)
            ->where('is_committed', false)
            ->count();
    }
}
