<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Modules\IAM\Livewire\UserCreate;
use Modules\IAM\Livewire\UserEdit;
use Modules\IAM\Livewire\UserIndex;

uses(RefreshDatabase::class);

it('allows managers to view user index', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserIndex::class)
        ->assertSuccessful();
});

it('limits non global managers to same department in user index', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');
    $otherDepartmentId = DB::table('departments')->insertGetId([
        'code' => 'SALES',
        'name' => 'Sales',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $sameDepartmentUser = User::factory()->create(['department_id' => $departmentId]);
    $otherDepartmentUser = User::factory()->create(['department_id' => $otherDepartmentId]);

    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserIndex::class)
        ->assertSee($sameDepartmentUser->email)
        ->assertDontSee($otherDepartmentUser->email);
});

it('allows admin portal to view users across departments in user index', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');
    $otherDepartmentId = DB::table('departments')->insertGetId([
        'code' => 'OPS',
        'name' => 'Operations',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $admin = User::factory()->create(['department_id' => $departmentId]);
    $admin->assignRole('Admin Portal');

    $user1 = User::factory()->create(['department_id' => $departmentId]);
    $user2 = User::factory()->create(['department_id' => $otherDepartmentId]);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->assertSee($user1->email)
        ->assertSee($user2->email);
});

it('creates user with department and roles', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserCreate::class)
        ->set('name', 'Portal User')
        ->set('email', 'portal-user@example.com')
        ->set('departmentId', (string) $departmentId)
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('selectedRoles', ['Requester'])
        ->call('save')
        ->assertRedirect();

    $createdUser = User::query()->where('email', 'portal-user@example.com')->firstOrFail();

    expect($createdUser->department_id)->toBe($departmentId);
    expect($createdUser->hasRole('Requester'))->toBeTrue();
});

it('updates user department and roles', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $manager = User::factory()->create(['department_id' => $departmentId]);
    $target = User::factory()->create(['department_id' => $departmentId]);

    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserEdit::class, ['user' => $target])
        ->set('departmentId', (string) $departmentId)
        ->set('selectedRoles', ['Requester'])
        ->call('save')
        ->assertSuccessful();

    expect($target->fresh()->department_id)->toBe($departmentId);
    expect($target->fresh()->hasRole('Requester'))->toBeTrue();
});

it('shows grouped permission map in user edit', function () {
    $this->seed();
    $departmentId = DB::table('departments')->value('id');

    $admin = User::factory()->create(['department_id' => $departmentId]);
    $admin->assignRole('Admin Portal');
    $target = User::factory()->create(['department_id' => $departmentId]);

    Livewire::actingAs($admin)
        ->test(UserEdit::class, ['user' => $target])
        ->assertSee('Permission map')
        ->assertSee('Item Management')
        ->assertSee('Administration')
        ->assertSee('item-request.validate')
        ->assertSee('user.manage');
});
