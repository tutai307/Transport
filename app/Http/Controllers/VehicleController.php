<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('defaultDriver')
            ->withCount('trips')
            ->orderBy('is_active', 'desc')
            ->orderBy('plate_number')
            ->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = Employee::active()->orderBy('name')->get();

        return view('vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'default_driver_id' => 'nullable|exists:employees,id',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Đã thêm xe.');
    }

    // AJAX: thêm xe nhanh từ modal trong form nhập chuyến xe.
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'default_driver_id' => 'nullable|exists:employees,id',
        ]);

        $vehicle = Vehicle::create([
            'plate_number' => trim($validated['plate_number']),
            'default_driver_id' => $validated['default_driver_id'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $vehicle->id,
            'plate_number' => $vehicle->plate_number,
            'default_driver_id' => $vehicle->default_driver_id,
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        $drivers = Employee::active()->orderBy('name')->get();

        return view('vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'default_driver_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Đã cập nhật xe.');
    }
}
