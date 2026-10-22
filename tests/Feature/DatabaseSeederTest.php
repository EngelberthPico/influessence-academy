<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('the database seeder does not create the test user in production', function () {
    app()->instance('env', 'production');

    app(DatabaseSeeder::class)->run();

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('the database seeder creates the test user outside production', function () {
    app(DatabaseSeeder::class)->run();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
