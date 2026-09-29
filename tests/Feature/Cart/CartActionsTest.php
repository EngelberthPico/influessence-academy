<?php

use App\Actions\Cart\AddCourseToCartAction;
use App\Actions\Cart\ClearCartAction;
use App\Actions\Cart\GetCartCoursesAction;
use App\Actions\Cart\RemoveCourseFromCartAction;
use App\Models\Course;

test('a course can be added to the cart', function () {
    $course = Course::factory()->create();

    (new AddCourseToCartAction)->handle($course->id);

    expect(session('cart'))->toBe([$course->id]);
});

test('adding the same course twice does not duplicate it in the cart', function () {
    $course = Course::factory()->create();

    $action = new AddCourseToCartAction;
    $action->handle($course->id);
    $action->handle($course->id);

    expect(session('cart'))->toBe([$course->id]);
});

test('a course can be removed from the cart', function () {
    $courseA = Course::factory()->create();
    $courseB = Course::factory()->create();

    $addAction = new AddCourseToCartAction;
    $addAction->handle($courseA->id);
    $addAction->handle($courseB->id);

    (new RemoveCourseFromCartAction)->handle($courseA->id);

    expect(session('cart'))->toBe([$courseB->id]);
});

test('removing a course that is not in the cart does nothing', function () {
    $course = Course::factory()->create();

    (new RemoveCourseFromCartAction)->handle($course->id);

    expect(session('cart', []))->toBe([]);
});

test('the cart courses are returned as course models', function () {
    $courseA = Course::factory()->create();
    $courseB = Course::factory()->create();

    session(['cart' => [$courseA->id, $courseB->id]]);

    $courses = (new GetCartCoursesAction)->handle();

    expect($courses)->toHaveCount(2)
        ->and($courses->pluck('id')->all())->toEqualCanonicalizing([$courseA->id, $courseB->id]);
});

test('the cart is empty when the session has no courses', function () {
    $courses = (new GetCartCoursesAction)->handle();

    expect($courses)->toBeEmpty();
});

test('the cart can be cleared completely', function () {
    $course = Course::factory()->create();
    session(['cart' => [$course->id]]);

    (new ClearCartAction)->handle();

    expect(session('cart', []))->toBe([]);
});
