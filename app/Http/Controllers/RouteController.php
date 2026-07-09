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
            'price' => 'nullable|numeric|min:0',
        ]);

        Route::create($validated);

        return redirect()->route('routes.index')->with('success', 'Đã thêm tuyến đường.');
    }

    public function edit(Route $route)
    {
        return view('routes.edit', compact('route'));
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location'   => 'required|string|max:255',
            'price'         => 'nullable|numeric|min:0',
        ]);

        $route = Route::create([
            'from_location' => trim($validated['from_location']),
            'to_location'   => trim($validated['to_location']),
            'price'         => $validated['price'] ?? 0,
            'is_active'     => true,
        ]);

        return response()->json([
            'id'        => $route->id,
            'full_name' => $route->full_name,
            'price'     => (float) $route->price,
        ]);
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $route->update($validated);

        return redirect()->route('routes.index')->with('success', 'Đã cập nhật tuyến đường.');
    }

    // AJAX: sửa nhanh giá cước của 1 cung chặng (dùng trong modal chọn cung chặng khi nhập chuyến xe).
    public function updatePrice(Request $request, Route $route)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $route->update(['price' => $validated['price']]);

        return response()->json([
            'id'    => $route->id,
            'price' => (float) $route->price,
        ]);
    }
}
