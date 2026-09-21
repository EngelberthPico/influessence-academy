<?php

use App\Enums\CourseType;
use App\Models\Course;

test('the catalog shows a message when there are no published courses', function () {
    Course::factory()->create(['is_published' => false]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee('Todavía no hay cursos publicados');
});

test('the catalog only shows published courses grouped by type', function () {
    $liveProgram = Course::factory()->create([
        'title' => 'Mentorship',
        'type' => CourseType::LiveProgram,
        'is_published' => true,
    ]);
    $hybrid = Course::factory()->create([
        'title' => 'Full Content',
        'type' => CourseType::Hybrid,
        'is_published' => true,
    ]);
    $recorded = Course::factory()->create([
        'title' => 'Amazon Links',
        'type' => CourseType::Recorded,
        'is_published' => true,
    ]);
    $unpublished = Course::factory()->create([
        'title' => 'Curso sin publicar',
        'is_published' => false,
    ]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee($liveProgram->title);
    $response->assertSee($hybrid->title);
    $response->assertSee($recorded->title);
    $response->assertDontSee($unpublished->title);
});

test('the catalog shows published courses regardless of best seller status', function () {
    $bestSeller = Course::factory()->bestSeller()->create(['title' => 'Curso Estrella', 'is_published' => true]);
    $regular = Course::factory()->create(['title' => 'Curso Normal', 'is_published' => true, 'is_best_seller' => false]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee($bestSeller->title);
    $response->assertSee($regular->title);
});

test('a published course can be viewed', function () {
    $course = Course::factory()->create(['is_published' => true]);

    $response = $this->get(route('courses.show', $course));

    $response->assertOk();
    $response->assertSee($course->title);
});

test('an unpublished course returns a 404', function () {
    $course = Course::factory()->create(['is_published' => false]);

    $response = $this->get(route('courses.show', $course));

    $response->assertNotFound();
});
