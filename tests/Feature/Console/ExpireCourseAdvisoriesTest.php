<?php

use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('a hybrid advisory past its window with a booking linked to the course is not expired', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $courseAccess = CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->subDay(),
    ]);

    Booking::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $this->artisan('courses:expire-advisories')->assertSuccessful();

    expect($courseAccess->fresh()->advisory_expired_at)->toBeNull();
});

test('a hybrid advisory past its window without a booking is expired', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $courseAccess = CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->subDay(),
    ]);

    $this->artisan('courses:expire-advisories')->assertSuccessful();

    expect($courseAccess->fresh()->advisory_expired_at)->not->toBeNull();
});

test('an advisory still within its window is not expired', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $courseAccess = CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->addDay(),
    ]);

    $this->artisan('courses:expire-advisories')->assertSuccessful();

    expect($courseAccess->fresh()->advisory_expired_at)->toBeNull();
});
