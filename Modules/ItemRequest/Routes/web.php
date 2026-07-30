<?php

use Illuminate\Support\Facades\Route;

Route::livewire('item-requests', 'item-request.index')->name('item-requests.index');
Route::livewire('item-requests/create', 'item-request.create')->name('item-requests.create');
Route::livewire('item-requests/{itemRequest}', 'item-request.show')->name('item-requests.show');
Route::livewire('validation-inbox', 'item-request.inbox')->name('item-requests.validation-inbox');
Route::livewire('accounting-inbox', 'item-request.inbox')->name('item-requests.accounting-inbox');
Route::livewire('approval-inbox', 'item-request.inbox')->name('item-requests.approval-inbox');
