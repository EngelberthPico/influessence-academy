<?php

use App\Models\Course;

test('formatPrice shows whole dollars without decimals when there are no cents', function (int $cents, string $expected) {
    expect(Course::formatPrice($cents))->toBe($expected);
})->with([
    [0, '$0'],
    [100, '$1'],
    [6900, '$69'],
    [7000, '$70'],
    [123400, '$1,234'],
]);

test('formatPrice shows two decimals when the price has cents', function (int $cents, string $expected) {
    expect(Course::formatPrice($cents))->toBe($expected);
})->with([
    [99, '$0.99'],
    [6950, '$69.50'],
    [6999, '$69.99'],
]);
