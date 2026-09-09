<?php

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('a user without an active course access does not have access to the course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    expect($user->hasAccessTo($course))->toBeFalse();
});

test('a user with a course access that has not been revoked has access to the course', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'revoked_at' => null,
    ]);

    expect($user->hasAccessTo($course))->toBeTrue();
});
