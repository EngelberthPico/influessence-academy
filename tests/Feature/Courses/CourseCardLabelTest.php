<?php

use App\Enums\CourseType;
use App\Models\Course;

test('card_label uses the duration label for live program courses', function () {
    $course = Course::factory()->make([
        'type' => CourseType::LiveProgram,
        'duration_months' => 3,
        'session_count' => null,
    ]);

    expect($course->card_label)->toBe('3 meses');
});

test('card_label is curso + asesoria for hybrid courses', function () {
    $course = Course::factory()->make(['type' => CourseType::Hybrid]);

    expect($course->card_label)->toBe('Curso + asesoría');
});

test('card_label is grabado for recorded courses', function () {
    $course = Course::factory()->make(['type' => CourseType::Recorded]);

    expect($course->card_label)->toBe('Grabado');
});
