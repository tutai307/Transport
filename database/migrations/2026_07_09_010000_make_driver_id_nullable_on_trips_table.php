<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->change();
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable(false)->change();
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreign('driver_id')->references('id')->on('employees');
        });
    }
};
