<?php

use App\Enums\CourseType;
use App\Models\Course;

test('the home shows published courses marked as best seller', function () {
    $bestSeller = Course::factory()->bestSeller()->create([
        'title' => 'Curso Estrella',
        'type' => CourseType::LiveProgram,
        'is_published' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Los cursos más vendidos');
    $response->assertSee($bestSeller->title);
});

test('the home does not show published courses that are not marked as best seller', function () {
    $regular = Course::factory()->create([
        'title' => 'Curso Regular',
        'is_published' => true,
        'is_best_seller' => false,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee($regular->title);
});

test('the home does not show best seller courses that are not published', function () {
    $unpublished = Course::factory()->bestSeller()->create([
        'title' => 'Curso Sin Publicar',
        'is_published' => false,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee($unpublished->title);
});

test('the home shows a fallback message and catalog link when there are no best seller courses', function () {
    Course::factory()->create(['is_published' => true, 'is_best_seller' => false]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Nuestros cursos');
    $response->assertSee('Explora el catálogo completo');
    $response->assertSee(route('courses.index'), false);
});

test('the home no longer shows the old catalog subtitles', function () {
    Course::factory()->bestSeller()->create(['type' => CourseType::LiveProgram, 'is_published' => true]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('Catálogo de cursos grabados');
    $response->assertDontSee('Acompañamiento en vivo con Fabiola');
    $response->assertDontSee('Curso grabado con acompañamiento final');
});

test('the home hero button and ver mas links point to the catalog instead of the cursos anchor', function () {
    $response = $this->get(route('home'));
    $content = $response->getContent();

    $response->assertOk();
    expect(substr_count($content, 'href="'.route('courses.index').'"'))->toBeGreaterThanOrEqual(6);
});
