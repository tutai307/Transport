<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = ['users', 'vehicles', 'employees'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // 1. Users
        User::create([
            'name' => 'Giám đốc Thảo',
            'email' => 'ntt@bn.vn',
            'password' => Hash::make('Mk12345@'),
            'is_admin' => true,
        ]);
        User::create([
            'name' => 'Giám đốc Tùng',
            'email' => 'thanhtung@bn.vn',
            'password' => Hash::make('Mk12345@'),
            'is_admin' => false,
        ]);

        // 2. Vehicles & Drivers from list
        $data = [
            ['plate' => '34C-1995', 'driver' => 'Tiến'],
            ['plate' => '15C-234.50', 'driver' => 'Vinh'],
            ['plate' => '19C-216.76', 'driver' => 'Quyên'],
            ['plate' => '37C-229.51', 'driver' => 'Cương'],
            ['plate' => '16M-1.170', 'driver' => 'Hướng'],
            ['plate' => '15C-020.66', 'driver' => 'Tuấn'],
            ['plate' => '89K-5250', 'driver' => 'Ba'],
            ['plate' => '20C-009.00', 'driver' => 'Đào'],
            ['plate' => '34M-0205', 'driver' => 'Chung'],
            ['plate' => '99C-307.23', 'driver' => 'Hoà'],
            ['plate' => '99C-037.55', 'driver' => 'Thuyết'],
            ['plate' => '-4263', 'driver' => 'Thắng'],
            ['plate' => '-4029', 'driver' => 'Nguyên'],
        ];

        foreach ($data as $item) {
            Employee::create([
                'name' => $item['driver'],
                'phone' => '',
                'is_active' => true
            ]);

            Vehicle::create([
                'plate_number' => $item['plate'],
                'default_volume_m3' => 5,
                'is_active' => true
            ]);
        }
    }
}
