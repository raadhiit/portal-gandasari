<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('allows user managers to view users in same department', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $target = User::factory()->create(['department_id' => $departmentId]);

    $manager->givePermissionTo('user.manage');

    expect($manager->can('view', $target))->toBeTrue();
});

it('denies user viewers from viewing users in different departments', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $viewer = User::factory()->create(['department_id' => $departmentId]);
    $target = User::factory()->create(['department_id' => null]);

    Permission::findOrCreate('user.view', 'web');
    $viewer->givePermissionTo('user.view');

    expect($viewer->can('view', $target))->toBeFalse();
});

it('allows admin portal to update users across departments', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');
    $otherDepartmentId = DB::table('departments')->insertGetId([
        'code' => 'HR',
        'name' => 'HR',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $admin = User::factory()->create(['department_id' => $departmentId]);
    $target = User::factory()->create(['department_id' => $otherDepartmentId]);

    $admin->assignRole('Admin Portal');

    expect($admin->can('update', $target))->toBeTrue();
});

it('denies non global managers from updating users in different departments', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');
    $otherDepartmentId = DB::table('departments')->insertGetId([
        'code' => 'FIN',
        'name' => 'Finance',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $target = User::factory()->create(['department_id' => $otherDepartmentId]);

    $manager->givePermissionTo('user.manage');

    expect($manager->can('update', $target))->toBeFalse();
});
