<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_m3', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['route_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_material');
    }
};
