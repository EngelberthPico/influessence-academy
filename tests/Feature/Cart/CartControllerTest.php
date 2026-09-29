<?php

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;

test('a guest can add a course to the cart and see it on the cart page', function () {
    $course = Course::factory()->create(['title' => 'Curso de Estrategia']);

    $this->post(route('cart.add', $course))->assertRedirect(route('cart.index'));

    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertSee('Curso de Estrategia');
});

test('adding an unpublished course to the cart is not allowed', function () {
    $course = Course::factory()->create(['is_published' => false]);

    $response = $this->post(route('cart.add', $course));

    $response->assertNotFound();
});

test('a guest can remove a course from the cart', function () {
    $course = Course::factory()->create(['title' => 'Curso a quitar']);

    $this->post(route('cart.add', $course));
    $this->delete(route('cart.remove', $course))->assertRedirect(route('cart.index'));

    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertDontSee('Curso a quitar');
});

test('the cart page shows an empty state when there are no courses', function () {
    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertSee('Tu carrito está vacío');
});

test('the cart page shows the total price of the courses in the cart', function () {
    $courseA = Course::factory()->create(['title' => 'Curso A', 'price_cents' => 5000]);
    $courseB = Course::factory()->create(['title' => 'Curso B', 'price_cents' => 3000]);

    $this->post(route('cart.add', $courseA));
    $this->post(route('cart.add', $courseB));

    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertSeeText('$80');
});

test('cart and checkout routes do not require authentication', function () {
    $course = Course::factory()->create();

    $this->get(route('cart.index'))->assertOk();
    $this->post(route('cart.add', $course))->assertRedirect();
    $this->delete(route('cart.remove', $course))->assertRedirect();
    $this->get(route('cart.thanks'))->assertOk();
});

test('a logged in user cannot add a course they already have access to', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['title' => 'Curso ya comprado']);
    $access = CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->post(route('cart.add', $course));

    $response->assertRedirect(route('cart.index'));
    $response->assertSessionHas('error');

    $cartResponse = $this->actingAs($user)->get(route('cart.index'));
    $cartResponse->assertDontSee('Curso ya comprado');

    expect($access->fresh())->not->toBeNull();
    expect(CourseAccess::count())->toBe(1);
});

test('a logged in user can add a repeat-purchase course even if they already have access to it', function () {
    $user = User::factory()->create();
    $course = Course::factory()->allowsRepeatPurchase()->create(['title' => 'Asesoría 1:1']);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    $response = $this->actingAs($user)->post(route('cart.add', $course));

    $response->assertRedirect(route('cart.index'));
    $response->assertSessionMissing('error');

    $cartResponse = $this->actingAs($user)->get(route('cart.index'));
    $cartResponse->assertSee('Asesoría 1:1');
});

test('visiting the thanks page clears the cart', function () {
    $course = Course::factory()->create(['title' => 'Curso comprado']);

    $this->post(route('cart.add', $course));
    $this->get(route('cart.thanks'))->assertOk();

    $response = $this->get(route('cart.index'));

    $response->assertOk();
    $response->assertDontSee('Curso comprado');
});
