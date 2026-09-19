<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('curso/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('reservar-clase', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings/confirmar', [BookingController::class, 'confirm'])->name('bookings.confirm');
});

require __DIR__.'/settings.php';
