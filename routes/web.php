<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyLearningController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('curso/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/mi-cuenta')->name('dashboard');

    Route::get('reservar-clase', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings/confirmar', [BookingController::class, 'confirm'])->name('bookings.confirm');

    Route::get('mi-cuenta', [MyLearningController::class, 'index'])->name('learning.index');
    Route::get('mi-cuenta/curso/{course:slug}', [MyLearningController::class, 'show'])->name('learning.show');
});

require __DIR__.'/settings.php';
