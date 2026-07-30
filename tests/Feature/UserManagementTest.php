<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\IAM\Livewire\UserEdit;
use Modules\IAM\Livewire\UserIndex;

uses(RefreshDatabase::class);

it('allows managers to view user index', function () {
    $this->seed();

    $manager = User::factory()->create(['department_id' => 1]);
    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserIndex::class)
        ->assertSuccessful();
});

it('updates user department and roles', function () {
    $this->seed();

    $manager = User::factory()->create(['department_id' => 1]);
    $target = User::factory()->create(['department_id' => 1]);

    $manager->givePermissionTo('user.manage');

    Livewire::actingAs($manager)
        ->test(UserEdit::class, ['user' => $target])
        ->set('departmentId', '1')
        ->set('selectedRoles', ['Requester'])
        ->call('save')
        ->assertSuccessful();

    expect($target->fresh()->department_id)->toBe(1);
    expect($target->fresh()->hasRole('Requester'))->toBeTrue();
});
