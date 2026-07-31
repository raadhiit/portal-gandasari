<?php

use App\Http\Controllers\ItemRequestExportDownloadController;
use Illuminate\Support\Facades\Route;

Route::livewire('item-requests', 'item-request.index')->name('item-requests.index');
Route::livewire('item-requests/create', 'item-request.create')->name('item-requests.create');
Route::livewire('item-requests/exports', 'item-request.export-index')->name('item-requests.exports.index');
Route::get('item-request-exports/{itemRequestExport}/download', ItemRequestExportDownloadController::class)->name('item-requests.exports.download');
Route::livewire('item-requests/{itemRequest}', 'item-request.show')->name('item-requests.show');
Route::livewire('validation-inbox', 'item-request.inbox')->name('item-requests.validation-inbox');
Route::livewire('accounting-inbox', 'item-request.inbox')->name('item-requests.accounting-inbox');
