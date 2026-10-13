<?php

use App\Models\Course;

test('a published guide appears in the guides section of the catalog', function () {
    $guide = Course::factory()->guide()->create(['title' => 'Guía de Instagram', 'is_published' => true]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee($guide->title);
    $response->assertSee('Guías prácticas para leer en tu cuenta');
});

test('an unpublished guide does not appear in the catalog', function () {
    $guide = Course::factory()->guide()->create(['title' => 'Guía sin publicar', 'is_published' => false]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertDontSee($guide->title);
});

test('the home page renders without error when a guide is marked as best seller', function () {
    Course::factory()->guide()->bestSeller()->create(['title' => 'Guía estrella', 'is_published' => true]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Guía estrella');
});
