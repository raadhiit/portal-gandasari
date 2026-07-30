<?php

use Illuminate\Support\Facades\Route;

Route::livewire('admin/users', 'iam.user-index')->name('admin.users.index');
Route::livewire('admin/users/{user}/edit', 'iam.user-edit')->name('admin.users.edit');
