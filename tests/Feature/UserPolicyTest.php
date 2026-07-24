<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('allows user managers to view users in same department', function () {
    $this->seed();

    $manager = User::factory()->create(['department_id' => 1]);
    $target = User::factory()->create(['department_id' => 1]);

    $manager->givePermissionTo('user.manage');

    expect($manager->can('view', $target))->toBeTrue();
});

it('denies user viewers from viewing users in different departments', function () {
    $this->seed();

    $viewer = User::factory()->create(['department_id' => 1]);
    $target = User::factory()->create(['department_id' => null]);

    Permission::findOrCreate('user.view', 'web');
    $viewer->givePermissionTo('user.view');

    expect($viewer->can('view', $target))->toBeFalse();
});
