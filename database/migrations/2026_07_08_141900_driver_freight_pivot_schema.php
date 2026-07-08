<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop material pricing columns (giá nhập/bán quá biến động, không dùng nữa).
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['import_price', 'sell_price']);
        });

        // 2. Trip pivots to freight-only. Drop sell/buy/profit.
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['sell_price', 'buy_price', 'profit']);
        });

        // 3. Add default driver on vehicles (chọn xe → auto fill tài xế).
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('default_driver_id')
                ->nullable()
                ->after('plate_number')
                ->constrained('employees')
                ->nullOnDelete();
        });

        // 4. Snapshot fields on trips: name + plate at write time.
        //    Preserves history when a driver later moves to a different vehicle.
        Schema::table('trips', function (Blueprint $table) {
            $table->string('driver_name_snapshot')->nullable()->after('driver_id');
            $table->string('vehicle_plate_snapshot', 20)->nullable()->after('vehicle_id');
        });

        // 5. Backfill snapshots for existing rows. Chunked update, portable across
        //    MySQL and SQLite (avoids driver-specific UPDATE ... JOIN syntax).
        DB::table('trips')
            ->select('trips.id', 'employees.name as driver_name', 'vehicles.plate_number as plate')
            ->leftJoin('employees', 'trips.driver_id', '=', 'employees.id')
            ->leftJoin('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
            ->orderBy('trips.id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('trips')->where('id', $row->id)->update([
                        'driver_name_snapshot' => $row->driver_name,
                        'vehicle_plate_snapshot' => $row->plate,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['driver_name_snapshot', 'vehicle_plate_snapshot']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['default_driver_id']);
            $table->dropColumn('default_driver_id');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->decimal('sell_price', 12, 2)->default(0)->after('freight_price');
            $table->decimal('buy_price', 12, 2)->default(0)->after('sell_price');
            $table->decimal('profit', 15, 2)->default(0)->after('total_price');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('import_price', 12, 2)->default(0)->after('is_active');
            $table->decimal('sell_price', 12, 2)->default(0)->after('import_price');
        });
    }
};
