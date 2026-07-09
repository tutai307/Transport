<?php

use App\Http\Controllers\TripController;
use App\Http\Controllers\SalaryAdjustmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;

// Settings Hub
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

// Trang chủ redirect đến dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Tất cả route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Chấm công
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/commit', [AttendanceController::class, 'commit'])->name('attendance.commit');
    Route::post('api/attendance/mark', [AttendanceController::class, 'mark'])->name('api.attendance.mark');
    Route::post('api/attendance/unmark', [AttendanceController::class, 'unmark'])->name('api.attendance.unmark');
    Route::patch('api/attendance/config', [AttendanceController::class, 'updateConfig'])->name('api.attendance.config');
    Route::get('api/attendance/details', [AttendanceController::class, 'details'])->name('api.attendance.details');
    Route::get('api/attendance/pending', [AttendanceController::class, 'pending'])->name('api.attendance.pending');

    // Chuyến xe — luồng: Dự án → Tháng → Chi tiết
    Route::get('trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('trips/project/{project}', [TripController::class, 'byProject'])->name('trips.by-project');
    Route::get('trips/project/{project}/{year}/{month}', [TripController::class, 'byMonth'])->name('trips.by-month');
    Route::get('trips/project/{project}/{year}/{month}/{day}', [TripController::class, 'byDay'])->name('trips.by-day');
    Route::get('trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
    Route::put('trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    // Phát sinh lương
    Route::post('salary-adjustments', [SalaryAdjustmentController::class, 'store'])->name('salary-adjustments.store');
    Route::delete('salary-adjustments/{salaryAdjustment}', [SalaryAdjustmentController::class, 'destroy'])->name('salary-adjustments.destroy');

    // Danh mục
    Route::resource('projects', ProjectController::class)->except(['show', 'destroy']);
    Route::resource('vehicles', VehicleController::class)->except(['show', 'destroy']);
    Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);
    Route::resource('materials', MaterialController::class)->except(['show', 'destroy']);
    Route::resource('routes', RouteController::class)->except(['show', 'destroy']);


    // Báo cáo
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Tính lương tài xế
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/driver/{employee}', [PayrollController::class, 'byDriver'])->name('payroll.by-driver');
    Route::get('payroll/driver/{employee}/export', [PayrollController::class, 'export'])->name('payroll.export');
    Route::get('payroll/driver/{employee}/{year}', [PayrollController::class, 'byYear'])->name('payroll.by-year');
    Route::get('payroll/driver/{employee}/{year}/{month}', [PayrollController::class, 'byMonth'])->name('payroll.by-month');

    // API endpoints cho auto-fill (dùng bởi JavaScript)
    Route::get('api/vehicles/{vehicle}/default-driver', [TripController::class, 'getVehicleDefaultDriver'])->name('api.vehicle-default-driver');
    Route::post('api/routes/quick-create', [RouteController::class, 'quickStore'])->name('api.routes.quick-create');
    Route::post('api/vehicles/quick-create', [VehicleController::class, 'quickStore'])->name('api.vehicles.quick-create');
    Route::patch('api/routes/{route}/price', [RouteController::class, 'updatePrice'])->name('api.routes.update-price');

    // Profile (từ Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
