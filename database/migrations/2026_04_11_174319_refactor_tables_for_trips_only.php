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
        Schema::table('trips', function (Blueprint $table) {
            $table->renameColumn('volume_m3', 'quantity');
            $table->renameColumn('price_per_m3', 'sell_price');
            $table->renameColumn('cost_per_m3', 'buy_price');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('default_volume_m3');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->renameColumn('quantity', 'volume_m3');
            $table->renameColumn('sell_price', 'price_per_m3');
            $table->renameColumn('buy_price', 'cost_per_m3');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('default_volume_m3', 10, 2)->default(5.0);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('unit')->default('m³');
        });
    }
};
