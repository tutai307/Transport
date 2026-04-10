<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Material;
use App\Models\Route;
use App\Models\RouteMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = ['users', 'projects', 'trips', 'vehicles', 'employees', 'materials', 'routes'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // 1. Users
        $admin = User::create([
            'name' => 'Giám đốc Thảo',
            'email' => 'ntt@bn.vn',
            'password' => Hash::make('Mk12345@'),
            'is_admin' => true,
        ]);
        User::create([
            'name' => 'Nhân viên Điều hành',
            'email' => 'thanhtung@bn.vn',
            'password' => Hash::make('Mk12345@'),
            'is_admin' => false,
        ]);

        // 2. Projects
        $projects = [
            'Dự án Vinhomes Ocean Park 3',
            'Cầu Vĩnh Tuy - Giai đoạn 2',
            'KCN Yên Phong II-C (Bắc Ninh)',
            'Nhà máy Samsung Thái Nguyên',
            'Sân bay Quốc tế Long Thành'
        ];
        foreach ($projects as $name) {
            Project::create(['name' => $name, 'description' => 'Dự án trọng điểm 2026', 'is_active' => true]);
        }
        $projectModels = Project::all();

        // 3. Vehicles
        $vehicles = [
            ['plate' => '99C-123.45', 'vol' => 15],
            ['plate' => '29H-988.66', 'vol' => 12],
            ['plate' => '98C-567.89', 'vol' => 5],
            ['plate' => '30F-111.22', 'vol' => 22],
            ['plate' => '15H-004.56', 'vol' => 18],
        ];
        foreach ($vehicles as $v) {
            Vehicle::create([
                'plate_number' => $v['plate'],
                'default_volume_m3' => $v['vol'],
                'is_active' => true
            ]);
        }
        $vehicleModels = Vehicle::all();

        // 4. Employees (Drivers)
        $drivers = [
            'Trần Minh Quang', 'Lê Hải Đăng', 'Phạm Hoàng Nam', 'Đỗ Quốc Hùng',
            'Bùi Tiến Dũng', 'Ngô Xuân Hiếu', 'Vũ Hải Long', 'Phan Văn Đức'
        ];
        foreach ($drivers as $name) {
            Employee::create(['name' => $name, 'phone' => '09' . rand(10000000, 99999999), 'is_active' => true]);
        }
        $driverModels = Employee::all();

        // 5. Materials
        $materials = [
            ['name' => 'Cát vàng đổ bê tông', 'unit' => 'm3', 'import' => 185000, 'sell' => 260000],
            ['name' => 'Cát đen san lấp', 'unit' => 'm3', 'import' => 85000, 'sell' => 135000],
            ['name' => 'Đá 1x2 (Hòa Sơn)', 'unit' => 'm3', 'import' => 240000, 'sell' => 320000],
            ['name' => 'Đất đồi san lấp', 'unit' => 'm3', 'import' => 55000, 'sell' => 105000],
            ['name' => 'Đá hộc xây móng', 'unit' => 'm3', 'import' => 195000, 'sell' => 275000],
            ['name' => 'Sỏi trang trí', 'unit' => 'm3', 'import' => 160000, 'sell' => 225000],
            ['name' => 'Cấp phối đá dăm Type 1', 'unit' => 'm3', 'import' => 210000, 'sell' => 285000],
        ];
        foreach ($materials as $m) {
            Material::create([
                'name' => $m['name'],
                'unit' => $m['unit'],
                'import_price' => $m['import'],
                'sell_price' => $m['sell'],
                'is_active' => true
            ]);
        }
        $materialModels = Material::all();

        // 6. Routes
        $routes = [
            ['from' => 'Mỏ đá Hòa Sơn', 'to' => 'Công trường Cầu Vĩnh Tuy', 'dist' => 18],
            ['from' => 'Bãi cát Thượng Cát', 'to' => 'Dự án Ocean Park 3', 'dist' => 22],
            ['from' => 'Mỏ đất Sóc Sơn', 'to' => 'Nhà máy Samsung TN', 'dist' => 12],
            ['from' => 'Cảng Đình Vũ', 'to' => 'KCN Yên Phong II-C', 'dist' => 85],
            ['from' => 'Mỏ đá Lương Sơn', 'to' => 'Đường vành đai 4', 'dist' => 35],
            ['from' => 'Bến phà Khuyến Lương', 'to' => 'Dự án Vinhomes', 'dist' => 8],
        ];
        foreach ($routes as $r) {
            Route::create(['from_location' => $r['from'], 'to_location' => $r['to'], 'distance_km' => $r['dist'], 'is_active' => true]);
        }
        $routeModels = Route::all();

        // 7. Trips (100 trips)
        // Distribute across projects and months (Jan - Apr 2026)
        for ($i = 0; $i < 100; $i++) {
            $project = $projectModels->random();
            $vehicle = $vehicleModels->random();
            $driver = $driverModels->random();
            $material = $materialModels->random();
            $route = $routeModels->random();
            
            $volume = $vehicle->default_volume_m3 * (rand(90, 105) / 100); 
            $price_per_m3 = $material->sell_price;
            $cost_per_m3 = $material->import_price;
            $profit = ($price_per_m3 - $cost_per_m3) * $volume;
            $total_price = $price_per_m3 * $volume;

            $month = rand(1, 4);
            $day = rand(1, 28);
            $date = sprintf('2026-%02d-%02d', $month, $day);

            Trip::create([
                'trip_date' => $date,
                'project_id' => $project->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'material_id' => $material->id,
                'route_id' => $route->id,
                'volume_m3' => round($volume, 2),
                'price_per_m3' => $price_per_m3,
                'cost_per_m3' => $cost_per_m3,
                'profit' => $profit,
                'total_price' => $total_price,
                'note' => '',
            ]);
        }
    }
}
