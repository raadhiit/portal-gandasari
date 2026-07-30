<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->upsert([
            ['code' => 'PCS', 'name' => 'Pieces', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_active', 'updated_at']);

        DB::table('warehouses')->upsert([
            ['code' => 'WH-01', 'name' => 'Main Warehouse', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_active', 'updated_at']);

        DB::table('business_units')->upsert([
            ['code' => 'PT-GS', 'name' => 'PT Gandasari', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_active', 'updated_at']);

        DB::table('item_categories')->upsert([
            ['code' => 'RAW', 'name' => 'Raw Material', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_active', 'updated_at']);
    }
}
