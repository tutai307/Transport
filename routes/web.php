<?php

use App\Http\Controllers\TripController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Trang chủ redirect đến danh sách chuyến xe
Route::get('/', function () {
    return redirect()->route('trips.index');
});

// Tất cả route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {

    // Chuyến xe — luồng: Dự án → Tháng → Chi tiết
    Route::get('trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('trips/project/{project}', [TripController::class, 'byProject'])->name('trips.by-project');
    Route::get('trips/project/{project}/{year}/{month}', [TripController::class, 'byMonth'])->name('trips.by-month');
    Route::get('trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
    Route::put('trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    // Danh mục
    Route::resource('projects', ProjectController::class)->except(['show', 'destroy']);
    Route::resource('vehicles', VehicleController::class)->except(['show', 'destroy']);
    Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);
    Route::resource('materials', MaterialController::class)->except(['show', 'destroy']);
    Route::resource('routes', RouteController::class)->except(['show', 'destroy']);

    // Bảng giá tuyến + vật liệu
    Route::get('route-materials', [RouteMaterialController::class, 'index'])->name('route-materials.index');
    Route::post('route-materials', [RouteMaterialController::class, 'store'])->name('route-materials.store');
    Route::delete('route-materials/{routeMaterial}', [RouteMaterialController::class, 'destroy'])->name('route-materials.destroy');

    // Báo cáo
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // API endpoints cho auto-fill (dùng bởi JavaScript)
    Route::get('api/vehicle-volume/{vehicle}', [TripController::class, 'getVehicleVolume'])->name('api.vehicle-volume');
    Route::get('api/get-price', [TripController::class, 'getPrice'])->name('api.get-price');

    // Profile (từ Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
