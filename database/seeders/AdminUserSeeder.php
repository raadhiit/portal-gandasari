<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array{email: string, password: string} $initialAdmin */
        $initialAdmin = config('portal.initial_admin');

        $departmentId = DB::table('departments')
            ->where('code', 'GENERAL')
            ->value('id');

        $admin = User::updateOrCreate(
            ['email' => $initialAdmin['email']],
            [
                'name' => 'Administrator',
                'department_id' => $departmentId,
                'password' => Hash::make($initialAdmin['password']),
            ]
        );

        $admin->assignRole('Admin Portal');
    }
}
