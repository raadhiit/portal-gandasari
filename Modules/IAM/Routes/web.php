<?php

use Illuminate\Support\Facades\Route;

Route::livewire('admin/users', 'iam.user-index')->name('admin.users.index');
Route::livewire('admin/users/create', 'iam.user-create')->name('admin.users.create');
Route::livewire('admin/users/{user}/edit', 'iam.user-edit')->name('admin.users.edit');
Route::livewire('admin/roles', 'iam.role-index')->name('admin.roles.index');
Route::livewire('admin/roles/create', 'iam.role-create')->name('admin.roles.create');
Route::livewire('admin/roles/{role}/edit', 'iam.role-edit')->name('admin.roles.edit');
