<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('notifications', 'notification-center')->name('notifications.index');

    require base_path('Modules/ItemRequest/Routes/web.php');
    require base_path('Modules/IAM/Routes/web.php');
});

require __DIR__.'/settings.php';
