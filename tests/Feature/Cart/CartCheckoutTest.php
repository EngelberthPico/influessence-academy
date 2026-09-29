<?php

use App\Models\Course;
use App\Models\User;

test('paying with an empty cart redirects back to the cart without calling stripe', function () {
    $response = $this->post(route('cart.pay'));

    $response->assertRedirect(route('cart.index'));
});

test('the checkout session is created with a line item per course and the course ids in metadata', function () {
    $courseA = Course::factory()->create(['title' => 'Curso A', 'price_cents' => 5000, 'currency' => 'usd']);
    $courseB = Course::factory()->create(['title' => 'Curso B', 'price_cents' => 3000, 'currency' => 'usd']);

    $this->post(route('cart.add', $courseA));
    $this->post(route('cart.add', $courseB));

    $fakeSession = (object) ['url' => 'https://checkout.stripe.com/test-session'];

    $mock = Mockery::mock('alias:Stripe\Checkout\Session');
    $mock->shouldReceive('create')
        ->once()
        ->withArgs(function (array $params) use ($courseA, $courseB): bool {
            expect($params['mode'])->toBe('payment');
            expect($params['line_items'])->toHaveCount(2);

            expect($params['line_items'][0]['quantity'])->toBe(1);
            expect($params['line_items'][0]['price_data']['currency'])->toBe('usd');
            expect($params['line_items'][0]['price_data']['unit_amount'])->toBe($courseA->price_cents);
            expect($params['line_items'][0]['price_data']['product_data']['name'])->toBe($courseA->title);

            expect($params['line_items'][1]['price_data']['unit_amount'])->toBe($courseB->price_cents);
            expect($params['line_items'][1]['price_data']['product_data']['name'])->toBe($courseB->title);

            expect($params['metadata']['course_ids'])->toBe("{$courseA->id},{$courseB->id}");
            expect($params['metadata'])->not->toHaveKey('user_id');
            expect($params)->not->toHaveKey('customer_email');
            expect($params['success_url'])->toBe(route('cart.thanks').'?session_id={CHECKOUT_SESSION_ID}');
            expect($params['cancel_url'])->toBe(route('cart.index'));

            return true;
        })
        ->andReturn($fakeSession);

    $response = $this->post(route('cart.pay'));

    $response->assertRedirect('https://checkout.stripe.com/test-session');
});

test('the checkout session includes the logged in user id and email in metadata', function () {
    $user = User::factory()->create(['email' => 'estudiante@example.com']);
    $course = Course::factory()->create(['price_cents' => 5000, 'currency' => 'usd']);

    $this->actingAs($user)->post(route('cart.add', $course));

    $fakeSession = (object) ['url' => 'https://checkout.stripe.com/test-session'];

    $mock = Mockery::mock('alias:Stripe\Checkout\Session');
    $mock->shouldReceive('create')
        ->once()
        ->withArgs(function (array $params) use ($user): bool {
            expect($params['metadata']['user_id'])->toBe((string) $user->id);
            expect($params['customer_email'])->toBe('estudiante@example.com');

            return true;
        })
        ->andReturn($fakeSession);

    $response = $this->actingAs($user)->post(route('cart.pay'));

    $response->assertRedirect('https://checkout.stripe.com/test-session');
});
