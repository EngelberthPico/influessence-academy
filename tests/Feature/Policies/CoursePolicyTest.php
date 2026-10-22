<?php

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('a student with active access to the course is allowed', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    expect($user->can('access', $course))->toBeTrue();
});

test('a student without access to the course is denied', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    expect($user->can('access', $course))->toBeFalse();
});

test('a guest is denied', function () {
    $course = Course::factory()->create();

    expect(Gate::forUser(null)->allows('access', $course))->toBeFalse();
});

test('an admin without a purchase is still denied, same as before this rule lived in a policy', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $course = Course::factory()->create();

    expect($admin->can('access', $course))->toBeFalse();
});
