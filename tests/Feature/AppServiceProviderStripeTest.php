<?php

use Stripe\Stripe;

test('stripe api key is configured on application boot', function () {
    expect(Stripe::getApiKey())
        ->not->toBeNull()
        ->toBe(config('services.stripe.secret'));
});
