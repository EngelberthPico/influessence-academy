<?php

use App\Enums\CourseType;
use App\Models\Course;

test('the home shows real published courses instead of hardcoded content', function () {
    $liveProgram = Course::factory()->create([
        'title' => 'Mentoría Real',
        'type' => CourseType::LiveProgram,
        'price_cents' => 99900,
        'is_published' => true,
    ]);
    $hybrid = Course::factory()->create([
        'title' => 'Curso Híbrido Real',
        'type' => CourseType::Hybrid,
        'is_published' => true,
    ]);
    $recorded = Course::factory()->create([
        'title' => 'Curso Grabado Real',
        'type' => CourseType::Recorded,
        'is_published' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee($liveProgram->title);
    $response->assertSee($hybrid->title);
    $response->assertSee($recorded->title);
    $response->assertDontSee('Mentorship');
    $response->assertDontSee('Master Pro');
    $response->assertDontSee('Amazon Links');
});

test('the home shows a message when there are no published courses', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Todavía no hay cursos publicados');
});

test('the most expensive live program course is the one highlighted on the home', function () {
    $cheaper = Course::factory()->create([
        'title' => 'Asesoría Puntual',
        'type' => CourseType::LiveProgram,
        'price_cents' => 34900,
        'is_published' => true,
    ]);
    $mostExpensive = Course::factory()->create([
        'title' => 'Programa Premium',
        'type' => CourseType::LiveProgram,
        'price_cents' => 169900,
        'is_published' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSeeInOrder([$cheaper->title, $mostExpensive->title]);
});
