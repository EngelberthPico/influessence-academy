<?php

use App\Actions\Courses\GrantCourseAccessAction;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    $this->action = new GrantCourseAccessAction;
});

test('granting access to a course without a redemption window leaves advisory_redeemable_until empty', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['redemption_window_days' => null]);

    $access = $this->action->handle($user, $course);

    expect($access->user_id)->toBe($user->id)
        ->and($access->course_id)->toBe($course->id)
        ->and($access->order_id)->toBeNull()
        ->and($access->granted_at)->not->toBeNull()
        ->and($access->advisory_redeemable_until)->toBeNull();
});

test('granting access to a course with a redemption window sets advisory_redeemable_until that many days out', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['redemption_window_days' => 14]);

    $access = $this->action->handle($user, $course);

    expect($access->advisory_redeemable_until->isSameDay(now()->addDays(14)))->toBeTrue();
});

test('granting access links it to the given order', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $order = Order::factory()->create();

    $access = $this->action->handle($user, $course, $order);

    expect($access->order_id)->toBe($order->id);
});
