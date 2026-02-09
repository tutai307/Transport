<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::withCount('trips')->orderBy('is_active', 'desc')->orderBy('name')->get();

        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'import_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')->with('success', 'Đã thêm vật liệu.');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'import_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $material->update($validated);

        return redirect()->route('materials.index')->with('success', 'Đã cập nhật vật liệu.');
    }
}
