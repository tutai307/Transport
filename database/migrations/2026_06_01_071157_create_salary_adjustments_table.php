<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('salary_adjustments');
        Schema::create('salary_adjustments', function (Blueprint $table) {
            $table->id();
            $table->date('trip_date');
            $table->foreignId('driver_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('type')->default('addition'); // 'addition' or 'deduction'
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['trip_date', 'driver_id']);
            $table->index(['trip_date', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_adjustments');
    }
};
