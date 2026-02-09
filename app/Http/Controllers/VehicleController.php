<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::withCount('trips')->orderBy('is_active', 'desc')->orderBy('plate_number')->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'default_volume_m3' => 'required|numeric|min:0',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Đã thêm xe.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'default_volume_m3' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Đã cập nhật xe.');
    }
}
