<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_marks', function (Blueprint $table) {
            $table->id();
            $table->date('attendance_date');
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('freight_price', 12, 2)->default(0);
            $table->timestamp('marked_at');
            $table->boolean('is_committed')->default(false);
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['attendance_date', 'project_id', 'vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_marks');
    }
};
