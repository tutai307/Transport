<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::withCount('trips')->orderBy('is_active', 'desc')->orderBy('from_location')->get();

        return view('routes.index', compact('routes'));
    }

    public function create()
    {
        return view('routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
        ]);

        Route::create($validated);

        return redirect()->route('routes.index')->with('success', 'Đã thêm tuyến đường.');
    }

    public function edit(Route $route)
    {
        return view('routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $route->update($validated);

        return redirect()->route('routes.index')->with('success', 'Đã cập nhật tuyến đường.');
    }
}
