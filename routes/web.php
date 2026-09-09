<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('reservar-clase', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings/confirmar', [BookingController::class, 'confirm'])->name('bookings.confirm');
});

require __DIR__.'/settings.php';
