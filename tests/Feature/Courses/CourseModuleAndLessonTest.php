<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;

test('a course lists its modules ordered by position', function () {
    $course = Course::factory()->create();

    $second = CourseModule::factory()->for($course)->create(['position' => 2]);
    $first = CourseModule::factory()->for($course)->create(['position' => 1]);
    $third = CourseModule::factory()->for($course)->create(['position' => 3]);

    expect($course->modules->pluck('id')->all())
        ->toBe([$first->id, $second->id, $third->id]);
});

test('a module lists its lessons ordered by position', function () {
    $module = CourseModule::factory()->create();

    $second = Lesson::factory()->for($module, 'module')->create(['position' => 2]);
    $first = Lesson::factory()->for($module, 'module')->create(['position' => 1]);

    expect($module->lessons->pluck('id')->all())
        ->toBe([$first->id, $second->id]);
});

test('a course lists all of its lessons through its modules, ordered by module then lesson position', function () {
    $course = Course::factory()->create();

    $moduleB = CourseModule::factory()->for($course)->create(['position' => 2]);
    $moduleA = CourseModule::factory()->for($course)->create(['position' => 1]);

    $lessonB2 = Lesson::factory()->for($moduleB, 'module')->create(['position' => 2]);
    $lessonB1 = Lesson::factory()->for($moduleB, 'module')->create(['position' => 1]);
    $lessonA1 = Lesson::factory()->for($moduleA, 'module')->create(['position' => 1]);

    expect($course->lessons->pluck('id')->all())
        ->toBe([$lessonA1->id, $lessonB1->id, $lessonB2->id]);
});

test('deleting a module deletes its lessons', function () {
    $module = CourseModule::factory()->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $module->delete();

    expect(Lesson::find($lesson->id))->toBeNull();
});

test('deleting a course deletes its modules and lessons', function () {
    $course = Course::factory()->create();
    $module = CourseModule::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module, 'module')->create();

    $course->delete();

    expect(CourseModule::find($module->id))->toBeNull();
    expect(Lesson::find($lesson->id))->toBeNull();
});
