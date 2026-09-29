<?php

use App\Models\Course;

test('the privacy policy page is publicly accessible', function () {
    $response = $this->get(route('legal.privacy'));

    $response->assertOk();
    $response->assertSee('Política de Privacidad');
    $response->assertSee(route('legal.refunds'), false);
});

test('the terms of service page is publicly accessible', function () {
    $response = $this->get(route('legal.terms'));

    $response->assertOk();
    $response->assertSee('Términos de Servicio');
    $response->assertSee(route('legal.refunds'), false);
});

test('the refunds policy page is publicly accessible', function () {
    $response = $this->get(route('legal.refunds'));

    $response->assertOk();
    $response->assertSee('Política de Reembolsos');
    $response->assertSee('Todas las compras realizadas en Influessence Academy son finales');
});

test('the footer links to all three legal pages', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('legal.privacy'), false);
    $response->assertSee(route('legal.terms'), false);
    $response->assertSee(route('legal.refunds'), false);
});

test('the cart page links to the refunds policy near the pay button', function () {
    $course = Course::factory()->create();
    $this->post(route('cart.add', $course));

    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertSee('Todas las ventas son finales');
    $response->assertSee(route('legal.refunds'), false);
});
