<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = ['users'];
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
    }
}
