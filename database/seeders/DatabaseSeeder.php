<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            RoleAndPermissionSeeder::class,
            MasterDataSeeder::class,
            CoaSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
