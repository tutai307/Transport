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
        Schema::table('materials', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
            $table->string('unit')->nullable()->after('name');
            $table->decimal('import_price', 12, 2)->default(0)->after('is_active');
            $table->decimal('sell_price', 12, 2)->default(0)->after('import_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['code', 'unit', 'import_price', 'sell_price']);
        });
    }
};
