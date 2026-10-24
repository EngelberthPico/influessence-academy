<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyProductRedirectController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MyLearningController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('cursos', [CourseController::class, 'index'])->name('courses.index');
Route::get('curso/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

Route::get('carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('carrito/agregar/{course}', [CartController::class, 'add'])->name('cart.add');
Route::delete('carrito/quitar/{course}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('carrito/pagar', [CartController::class, 'pay'])->name('cart.pay');
Route::get('carrito/gracias', [CartController::class, 'thanks'])->name('cart.thanks');

Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('politica-de-privacidad', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('terminos-de-servicio', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('politica-de-reembolsos', [LegalController::class, 'refunds'])->name('legal.refunds');

// Redirecciones del sitio anterior (WooCommerce), para que las URLs que Google
// ya tiene indexadas no caigan en un 404.
Route::get('product/{slug}', LegacyProductRedirectController::class)->name('legacy.product');
Route::redirect('cart', '/carrito', 301);
Route::redirect('my-account', '/mi-cuenta', 301);
Route::redirect('shop', '/cursos', 301);
Route::redirect('checkout', '/carrito', 301);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/mi-cuenta')->name('dashboard');

    Route::get('reservar-clase', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('bookings/confirmar', [BookingController::class, 'confirm'])->name('bookings.confirm');

    Route::get('mi-cuenta', [MyLearningController::class, 'index'])->name('learning.index');
    Route::get('mi-cuenta/curso/{course:slug}', [MyLearningController::class, 'show'])->name('learning.show');
    Route::get('mi-cuenta/curso/{course:slug}/video/{lesson}', [MyLearningController::class, 'lesson'])
        ->name('learning.lesson')
        ->scopeBindings();
    Route::get('mi-cuenta/guia/{course:slug}/pagina/{page}', [MyLearningController::class, 'guidePage'])
        ->name('learning.guide-page')
        ->whereNumber('page');
});

require __DIR__.'/settings.php';
