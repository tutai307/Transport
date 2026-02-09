<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Material;
use App\Models\RouteMaterial;
use Illuminate\Http\Request;

class RouteMaterialController extends Controller
{
    public function index()
    {
        $routeMaterials = RouteMaterial::with(['route', 'material'])->get()
            ->groupBy('route_id');
        $routes = Route::active()->orderBy('from_location')->get();
        $materials = Material::active()->orderBy('name')->get();

        return view('route_materials.index', compact('routeMaterials', 'routes', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'material_id' => 'required|exists:materials,id',
            'price_per_m3' => 'required|numeric|min:0',
        ]);

        RouteMaterial::updateOrCreate(
            [
                'route_id' => $validated['route_id'],
                'material_id' => $validated['material_id'],
            ],
            ['price_per_m3' => $validated['price_per_m3']]
        );

        return redirect()->route('route-materials.index')->with('success', 'Đã cập nhật giá.');
    }

    public function destroy(RouteMaterial $routeMaterial)
    {
        $routeMaterial->delete();

        return redirect()->route('route-materials.index')->with('success', 'Đã xoá giá.');
    }
}
