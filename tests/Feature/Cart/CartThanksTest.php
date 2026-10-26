<?php

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Stripe\Exception\InvalidRequestException;

function fakeStripeCheckoutSessionForThanks(array $overrides = []): object
{
    return (object) array_merge([
        'id' => 'cs_test_thanks',
        'amount_total' => 8000,
        'currency' => 'usd',
        'customer_details' => (object) ['email' => 'compradora@example.com'],
        'metadata' => (object) ['course_ids' => ''],
        'line_items' => (object) ['data' => []],
    ], $overrides);
}

function mockStripeThanksRetrieve(string $sessionId, object $session): void
{
    Mockery::mock('alias:Stripe\Checkout\Session')
        ->shouldReceive('retrieve')
        ->once()
        ->with(['id' => $sessionId, 'expand' => ['line_items']])
        ->andReturn($session);
}

test('a logged in user with access already granted sees the purchased courses and a link to their account', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['title' => 'Curso Comprado']);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    mockStripeThanksRetrieve('cs_test_thanks', fakeStripeCheckoutSessionForThanks([
        'amount_total' => 5000,
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]));

    $response = $this->actingAs($user)->get(route('cart.thanks', ['session_id' => 'cs_test_thanks']));

    $response->assertOk();
    $response->assertSee('Curso Comprado');
    $response->assertSee('$50');
    $response->assertSee(route('learning.show', $course), false);
    $response->assertDontSee('Revisa el correo');
    $response->assertDontSee('Estamos activando tu acceso');
});

test('a logged in user whose access is not granted yet sees an activating-access message instead', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['title' => 'Curso Comprado']);

    mockStripeThanksRetrieve('cs_test_thanks', fakeStripeCheckoutSessionForThanks([
        'amount_total' => 5000,
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]));

    $response = $this->actingAs($user)->get(route('cart.thanks', ['session_id' => 'cs_test_thanks']));

    $response->assertOk();
    $response->assertSee('¡Pago recibido!');
    $response->assertSee('Estamos activando tu acceso a Curso Comprado');
    $response->assertSee(route('learning.index'), false);
    $response->assertDontSee(route('learning.show', $course), false);
});

test('a guest sees a thank you message with the email that will receive access', function () {
    $course = Course::factory()->create(['title' => 'Curso de Invitada']);

    mockStripeThanksRetrieve('cs_test_thanks', fakeStripeCheckoutSessionForThanks([
        'amount_total' => 4000,
        'customer_details' => (object) ['email' => 'invitada@example.com'],
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]));

    $response = $this->get(route('cart.thanks', ['session_id' => 'cs_test_thanks']));

    $response->assertOk();
    $response->assertSee('Curso de Invitada');
    $response->assertSee('invitada@example.com');
    $response->assertSee('$40');
});

test('the thanks page shows the total paid with cents instead of rounding it', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create(['title' => 'Curso Comprado']);
    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

    mockStripeThanksRetrieve('cs_test_thanks', fakeStripeCheckoutSessionForThanks([
        'amount_total' => 6999,
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]));

    $response = $this->actingAs($user)->get(route('cart.thanks', ['session_id' => 'cs_test_thanks']));

    $response->assertOk();
    $response->assertSee('$69.99');
    $response->assertDontSee('$70');
});

test('a missing session id falls back to the generic thank you message', function () {
    $response = $this->get(route('cart.thanks'));

    $response->assertOk();
    $response->assertSee('¡Gracias por tu compra!');
});

test('a session id stripe cannot resolve falls back to the generic thank you message', function () {
    Mockery::mock('alias:Stripe\Checkout\Session')
        ->shouldReceive('retrieve')
        ->once()
        ->with(['id' => 'cs_invalid', 'expand' => ['line_items']])
        ->andThrow(new InvalidRequestException('No such checkout session.'));

    $response = $this->get(route('cart.thanks', ['session_id' => 'cs_invalid']));

    $response->assertOk();
    $response->assertSee('¡Gracias por tu compra!');
});
