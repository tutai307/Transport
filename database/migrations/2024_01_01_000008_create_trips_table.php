<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->date('trip_date');
            $table->foreignId('project_id')->constrained();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('driver_id')->constrained('employees');
            $table->foreignId('material_id')->constrained();
            $table->foreignId('route_id')->constrained();
            $table->decimal('volume_m3', 8, 2)->default(0);
            $table->decimal('price_per_m3', 12, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['trip_date', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
