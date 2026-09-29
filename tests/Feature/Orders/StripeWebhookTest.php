<?php

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Stripe\Exception\SignatureVerificationException;

function fakeStripeCheckoutSession(array $overrides = []): object
{
    return (object) array_merge([
        'id' => 'cs_test_'.Str::random(16),
        'payment_intent' => 'pi_test_'.Str::random(16),
        'amount_total' => 8000,
        'currency' => 'usd',
        'customer_details' => (object) [
            'email' => 'compradora@example.com',
            'name' => 'Compradora Ejemplo',
        ],
        'metadata' => (object) [
            'course_ids' => '',
        ],
    ], $overrides);
}

function fakeStripeCheckoutEvent(string $type, object $session): object
{
    return (object) [
        'type' => $type,
        'data' => (object) [
            'object' => $session,
        ],
    ];
}

function mockStripeLineItemsRetrieve(string $sessionId, array $amounts): void
{
    $expandedSession = (object) [
        'id' => $sessionId,
        'line_items' => (object) [
            'data' => array_map(fn (int $amount) => (object) ['amount_total' => $amount], $amounts),
        ],
    ];

    Mockery::mock('alias:Stripe\Checkout\Session')
        ->shouldReceive('retrieve')
        ->once()
        ->with(['id' => $sessionId, 'expand' => ['line_items']])
        ->andReturn($expandedSession);
}

test('an invalid webhook signature is rejected and nothing is created', function () {
    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andThrow(new SignatureVerificationException('Invalid signature.'));

    $response = $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'invalid']);

    $response->assertStatus(400);
    expect(Order::count())->toBe(0);
});

test('an unrelated event type is acknowledged without creating anything', function () {
    $session = fakeStripeCheckoutSession();
    $event = fakeStripeCheckoutEvent('payment_intent.succeeded', $session);

    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andReturn($event);

    $response = $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid']);

    $response->assertOk();
    expect(Order::count())->toBe(0);
});

test('a completed checkout session for a new email creates the user, order, order items, course access and sends a reset link', function () {
    Notification::fake();

    $courseA = Course::factory()->create(['price_cents' => 5000]);
    $courseB = Course::factory()->create(['price_cents' => 3000, 'redemption_window_days' => 10]);

    $session = fakeStripeCheckoutSession([
        'amount_total' => 8000,
        'metadata' => (object) ['course_ids' => "{$courseA->id},{$courseB->id}"],
    ]);
    $event = fakeStripeCheckoutEvent('checkout.session.completed', $session);

    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andReturn($event);

    mockStripeLineItemsRetrieve($session->id, [5000, 3000]);

    $response = $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid']);

    $response->assertOk();

    $user = User::where('email', 'compradora@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Compradora Ejemplo')
        ->and($user->email_verified_at)->not->toBeNull();

    $order = Order::first();
    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBe($user->id)
        ->and($order->status)->toBe(OrderStatus::Paid)
        ->and($order->total_cents)->toBe(8000)
        ->and($order->stripe_checkout_session_id)->toBe($session->id)
        ->and($order->stripe_payment_intent_id)->toBe($session->payment_intent)
        ->and($order->paid_at)->not->toBeNull();

    expect(OrderItem::count())->toBe(2);
    expect(OrderItem::where('course_id', $courseA->id)->first()->price_cents)->toBe(5000);
    expect(OrderItem::where('course_id', $courseB->id)->first()->price_cents)->toBe(3000);

    expect(CourseAccess::where('user_id', $user->id)->where('course_id', $courseA->id)->exists())->toBeTrue();

    $accessB = CourseAccess::where('user_id', $user->id)->where('course_id', $courseB->id)->first();
    expect($accessB->advisory_redeemable_until)->not->toBeNull();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('a completed checkout session for an existing email links the purchase to that account', function () {
    Notification::fake();

    $existingUser = User::factory()->create(['email' => 'compradora@example.com']);
    $course = Course::factory()->create(['price_cents' => 4000]);

    $session = fakeStripeCheckoutSession([
        'amount_total' => 4000,
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]);
    $event = fakeStripeCheckoutEvent('checkout.session.completed', $session);

    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andReturn($event);

    mockStripeLineItemsRetrieve($session->id, [4000]);

    $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid'])->assertOk();

    expect(User::where('email', 'compradora@example.com')->count())->toBe(1);

    $order = Order::first();
    expect($order->user_id)->toBe($existingUser->id);

    Notification::assertNotSentTo($existingUser, ResetPassword::class);
});

test('a completed checkout session with a logged in user id in metadata is assigned to that account even if the stripe email differs', function () {
    Notification::fake();

    $loggedInUser = User::factory()->create(['email' => 'cuenta-logueada@example.com']);
    $course = Course::factory()->create(['price_cents' => 4000]);

    $session = fakeStripeCheckoutSession([
        'amount_total' => 4000,
        'customer_details' => (object) [
            'email' => 'tarjeta-de-otra-persona@example.com',
            'name' => 'Dueña de la Tarjeta',
        ],
        'metadata' => (object) [
            'course_ids' => (string) $course->id,
            'user_id' => (string) $loggedInUser->id,
        ],
    ]);
    $event = fakeStripeCheckoutEvent('checkout.session.completed', $session);

    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->once()
        ->andReturn($event);

    mockStripeLineItemsRetrieve($session->id, [4000]);

    $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid'])->assertOk();

    expect(User::count())->toBe(1);
    expect(User::where('email', 'tarjeta-de-otra-persona@example.com')->exists())->toBeFalse();

    $order = Order::first();
    expect($order->user_id)->toBe($loggedInUser->id);

    Notification::assertNotSentTo($loggedInUser, ResetPassword::class);
});

test('the same webhook event delivered twice does not duplicate the order or course access', function () {
    Notification::fake();

    $course = Course::factory()->create(['price_cents' => 4000]);

    $session = fakeStripeCheckoutSession([
        'amount_total' => 4000,
        'metadata' => (object) ['course_ids' => (string) $course->id],
    ]);
    $event = fakeStripeCheckoutEvent('checkout.session.completed', $session);

    Mockery::mock('alias:Stripe\Webhook')
        ->shouldReceive('constructEvent')
        ->twice()
        ->andReturn($event);

    mockStripeLineItemsRetrieve($session->id, [4000]);

    $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid'])->assertOk();
    $this->postJson(route('stripe.webhook'), [], ['Stripe-Signature' => 'valid'])->assertOk();

    expect(Order::count())->toBe(1);
    expect(OrderItem::count())->toBe(1);
    expect(CourseAccess::count())->toBe(1);
});
