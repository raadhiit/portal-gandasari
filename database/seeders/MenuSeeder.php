<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $dashboardId = DB::table('systems')->where('code', 'dashboard')->value('id');
        $itemManagementId = DB::table('systems')->where('code', 'item-management')->value('id');

        $menus = [
            ['system_id' => $dashboardId, 'code' => 'dashboard-overview', 'name' => 'Overview', 'route_name' => 'dashboard', 'permission_name' => 'dashboard.view', 'sort_order' => 10],
            ['system_id' => $itemManagementId, 'code' => 'item-requests', 'name' => 'Item Requests', 'route_name' => 'item-requests.index', 'permission_name' => 'item-request.view', 'sort_order' => 10],
        ];

        foreach ($menus as $menu) {
            DB::table('menus')->updateOrInsert(['code' => $menu['code']], [...$menu, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
