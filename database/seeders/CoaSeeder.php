<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('coas')->upsert([
            ['code' => '6000', 'name' => 'Raw Materials', 'department_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '6100', 'name' => 'Packaging', 'department_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => '7000', 'name' => 'Operational Expense', 'department_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'department_id', 'is_active', 'updated_at']);
    }
}
