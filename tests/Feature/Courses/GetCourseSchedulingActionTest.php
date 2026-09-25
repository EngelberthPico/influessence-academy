<?php

use App\Actions\Courses\GetCourseSchedulingAction;
use App\Enums\CourseType;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

beforeEach(function () {
    $this->action = new GetCourseSchedulingAction;
});

test('deadlinePassed is false when the redemption window is still open', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Hybrid]);
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->addDays(2),
    ]);

    $result = $this->action->handle($user, $course);

    expect($result['deadlinePassed'])->toBeFalse();
    expect($result['redeemableUntil'])->not->toBeNull();
});

test('deadlinePassed is true when the redemption window already closed', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Hybrid]);
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => now()->subDay(),
    ]);

    $result = $this->action->handle($user, $course);

    expect($result['deadlinePassed'])->toBeTrue();
});

test('deadlinePassed is always false when there is no redemption window', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['type' => CourseType::Hybrid]);
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'advisory_redeemable_until' => null,
    ]);

    $result = $this->action->handle($user, $course);

    expect($result['deadlinePassed'])->toBeFalse();
    expect($result['redeemableUntil'])->toBeNull();
});
