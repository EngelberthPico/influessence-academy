<?php

use App\Models\Course;

test('a mapped legacy product slug redirects permanently to its course when the course exists and is published', function () {
    $course = Course::factory()->create(['title' => 'Asesoría 1:1', 'is_published' => true]);
    expect($course->slug)->toBe('asesoria-11');

    $response = $this->get('/product/11-class');

    $response->assertStatus(301);
    $response->assertRedirect(route('courses.show', $course));
});

test('a mapped legacy product slug falls back to the catalog when the course does not exist', function () {
    $response = $this->get('/product/11-class');

    $response->assertStatus(301);
    $response->assertRedirect(route('courses.index'));
});

test('a mapped legacy product slug falls back to the catalog when the course exists but is not published', function () {
    Course::factory()->create(['title' => 'Asesoría 1:1', 'is_published' => false]);

    $response = $this->get('/product/11-class');

    $response->assertStatus(301);
    $response->assertRedirect(route('courses.index'));
});

test('an unknown legacy product slug falls back to the catalog', function () {
    $response = $this->get('/product/estratega');

    $response->assertStatus(301);
    $response->assertRedirect(route('courses.index'));
});

test('the common WooCommerce urls redirect permanently to their new equivalent', function () {
    $this->get('/cart')->assertStatus(301)->assertRedirect('/carrito');
    $this->get('/my-account')->assertStatus(301)->assertRedirect('/mi-cuenta');
    $this->get('/shop')->assertStatus(301)->assertRedirect('/cursos');
    $this->get('/checkout')->assertStatus(301)->assertRedirect('/carrito');
});
