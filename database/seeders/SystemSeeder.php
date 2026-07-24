<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            ['code' => 'dashboard', 'name' => 'Dashboard', 'icon' => 'home', 'sort_order' => 10],
            ['code' => 'item-management', 'name' => 'Item Management', 'icon' => 'archive-box', 'sort_order' => 20],
            ['code' => 'my-tasks', 'name' => 'My Tasks', 'icon' => 'clipboard-document-check', 'sort_order' => 30],
            ['code' => 'master-data', 'name' => 'Master Data', 'icon' => 'circle-stack', 'sort_order' => 40],
            ['code' => 'administration', 'name' => 'Administration', 'icon' => 'cog-6-tooth', 'sort_order' => 50],
            ['code' => 'reports', 'name' => 'Reports', 'icon' => 'chart-bar', 'sort_order' => 60],
            ['code' => 'budget', 'name' => 'Budget', 'icon' => 'banknotes', 'sort_order' => 70],
        ];

        DB::table('systems')->upsert(
            array_map(fn (array $system): array => [...$system, 'is_active' => $system['code'] !== 'budget', 'created_at' => now(), 'updated_at' => now()], $systems),
            ['code'],
            ['name', 'icon', 'sort_order', 'is_active', 'updated_at'],
        );
    }
}
