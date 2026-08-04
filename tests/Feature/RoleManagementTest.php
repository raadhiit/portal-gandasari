<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Modules\IAM\Livewire\RoleCreate;
use Modules\IAM\Livewire\RoleEdit;
use Modules\IAM\Livewire\RoleIndex;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows role managers to view role index', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $manager->givePermissionTo('role.manage');

    Livewire::actingAs($manager)
        ->test(RoleIndex::class)
        ->assertSuccessful();
});

it('creates role with granular permissions', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $manager->givePermissionTo('role.manage');

    Livewire::actingAs($manager)
        ->test(RoleCreate::class)
        ->set('name', 'COA Creator')
        ->set('selectedPermissions', ['coa.create'])
        ->call('save')
        ->assertRedirect();

    $role = Role::query()->where('name', 'COA Creator')->firstOrFail();

    expect($role->hasPermissionTo('coa.create'))->toBeTrue();
    expect($role->hasPermissionTo('coa.update'))->toBeFalse();
});

it('updates role granular permissions', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $manager->givePermissionTo('role.manage');

    $role = Role::findOrCreate('COA Operator', 'web');
    $role->syncPermissions(['coa.create']);

    Livewire::actingAs($manager)
        ->test(RoleEdit::class, ['role' => $role])
        ->set('selectedPermissions', ['coa.create', 'coa.update'])
        ->call('save')
        ->assertSuccessful();

    expect($role->fresh()->hasPermissionTo('coa.create'))->toBeTrue();
    expect($role->fresh()->hasPermissionTo('coa.update'))->toBeTrue();
});
